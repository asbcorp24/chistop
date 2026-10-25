<?php
namespace App\Services;

use App\Models\Discipline;
use App\Models\ImportBatch;
use App\Models\ImportError;
use App\Models\ScheduleLesson;
use App\Models\StudyGroup;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use Throwable;

class RectorXmlImportService
{
    public function run(ImportBatch $batch, string $contents): void
    {
        $batch->update(['status'=>'processing','started_at'=>now()]);
        try {
            $xml=simplexml_load_string($contents,'SimpleXMLElement',LIBXML_NONET|LIBXML_COMPACT);
            if(!$xml || $xml->getName()!=='timetable') throw new \RuntimeException('Некорректный Rector XML.');

            DB::transaction(function() use($batch,$xml){
                $org=(int)$batch->organization_id;
                $teachers=$this->teachers($xml,$org);
                $groups=$this->groups($xml,$org);
                $subjects=$this->subjects($xml,$org);
                $types=$this->studyTypes($xml);
                $rooms=$this->rooms($xml);
                $times=$this->times($xml);
                $loads=$this->loads($xml);

                ScheduleLesson::where('organization_id',$org)->where('source_data->source','rector_xml')->delete();

                $created=0;$skipped=0;
                foreach($xml->scheds->sched ?? [] as $sched){
                    $loadId=(string)$sched->load_id;
                    $load=$loads[$loadId]??null;
                    if(!$load){$skipped++;continue;}
                    $hour=(int)$sched->hour;
                    $start=$times[$hour]['start']??null;
                    $end=$times[$hour]['end']??null;
                    if(!$start||!$end){$skipped++;continue;}
                    // Rector uses adjacent 45-minute slots; a pkYes load is a 90-minute pair.
                    if(($load['pair_type']??'')==='pkYes' && isset($times[$hour+1])) $end=$times[$hour+1]['end'];
                    $begin=$this->date((string)$sched->begin_date);$finish=$this->date((string)$sched->end_date);
                    if(!$begin||!$finish){$skipped++;continue;}
                    $weekday=max(1,min(7,(int)$sched->day));
                    $date=$begin->copy();
                    while($date->dayOfWeekIso!==$weekday && $date->lte($finish)) $date->addDay();
                    foreach($load['class_ids'] as $classId){
                        $group=$groups[$classId]??null;$discipline=$subjects[$load['subject_id']]??null;
                        if(!$group||!$discipline){$skipped++;continue;}
                        for($d=$date->copy();$d->lte($finish);$d->addWeek()){
                            $external=implode(':',['rector',$loadId,$classId,$d->format('Ymd'),$hour,(string)$sched->group]);
                            ScheduleLesson::updateOrCreate(
                                ['organization_id'=>$org,'external_id'=>$external],
                                ['study_group_id'=>$group->id,'discipline_id'=>$discipline->id,'teacher_id'=>($teachers[$load['teacher_id']]??null)?->id,
                                 'lesson_date'=>$d->toDateString(),'pair_number'=>(int)ceil($hour/2),'starts_at'=>$start,'ends_at'=>$end,
                                 'room'=>$rooms[(string)$sched->room_id]??null,'subgroup'=>(string)$sched->group!=='0'?'Подгруппа '.((int)$sched->group+1):null,
                                 'lesson_type'=>$types[$load['study_type_id']]??null,'status'=>'scheduled','source_data'=>['source'=>'rector_xml','load_id'=>$loadId,'hour'=>$hour,'sched'=>json_decode(json_encode($sched),true)]]
                            );$created++;
                        }
                    }
                }
                $batch->update(['status'=>'completed','created_count'=>$created,'skipped_count'=>$skipped,'finished_at'=>now(),'meta'=>array_merge($batch->meta??[],['teachers'=>count($teachers),'classes'=>count($groups),'subjects'=>count($subjects),'loads'=>count($loads),'lessons'=>$created])]);
            });
        } catch(Throwable $e) {
            ImportError::create(['import_batch_id'=>$batch->id,'code'=>'IMPORT_FAILED','message'=>$e->getMessage()]);
            $batch->update(['status'=>'failed','error_count'=>1,'finished_at'=>now()]);
            throw $e;
        }
    }

    private function teachers(SimpleXMLElement $xml,int $org): array {$out=[];foreach($xml->teachers->teacher??[] as $t){$p=$t->person;$id=(string)$p->id;if($id==='')continue;$out[$id]=Teacher::updateOrCreate(['organization_id'=>$org,'external_id'=>'rector:'.$id],['last_name'=>trim((string)$p->surname)?:'Без фамилии','first_name'=>trim((string)$p->first_name)?:'-','middle_name'=>trim((string)$p->second_name)?:null,'is_active'=>true]);}return $out;}
    private function groups(SimpleXMLElement $xml,int $org): array {$out=[];foreach($xml->classes->class??[] as $g){$id=(string)$g->id;if($id==='')continue;$out[$id]=StudyGroup::updateOrCreate(['organization_id'=>$org,'external_id'=>'rector:'.$id],['name'=>trim((string)$g->name)?:('Группа '.$id),'status'=>'active']);}return $out;}
    private function subjects(SimpleXMLElement $xml,int $org): array {$out=[];foreach($xml->subjects->subject??[] as $s){$id=(string)$s->id;if($id==='')continue;$out[$id]=Discipline::updateOrCreate(['organization_id'=>$org,'external_id'=>'rector:'.$id],['name'=>trim((string)$s->full_name)?:trim((string)$s->short_name)?:('Дисциплина '.$id),'is_active'=>true]);}return $out;}
    private function studyTypes(SimpleXMLElement $xml): array {$o=[];foreach($xml->study_types->study_type??[] as $x)$o[(string)$x->id]=trim((string)$x->full_name);return $o;}
    private function rooms(SimpleXMLElement $xml): array {$o=[];foreach($xml->rooms->room??[] as $x)$o[(string)$x->id]=trim((string)($x->name?:$x->full_name?:$x->short_name));return $o;}
    private function times(SimpleXMLElement $xml): array {$o=[];$i=0;foreach($xml->times->time??[] as $x){$v=trim((string)$x);if(preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/u',$v,$m))$o[$i]=['start'=>$m[1].':00','end'=>$m[2].':00'];$i++;}return $o;}
    private function loads(SimpleXMLElement $xml): array {$o=[];foreach($xml->loads->load??[] as $l){$g=$l->groups->group[0]??null;if(!$g)continue;$ids=[];foreach($l->klass_id_list->int??[] as $x)$ids[]=(string)$x;$o[(string)$l->id]=['class_ids'=>$ids,'teacher_id'=>(string)$g->teacher_id,'subject_id'=>(string)$g->subject_id,'study_type_id'=>(string)$g->study_type_id,'pair_type'=>(string)$g->pair_type];}return $o;}
    private function date(string $v): ?Carbon {try{return Carbon::createFromFormat('d.m.Y',$v)->startOfDay();}catch(Throwable){return null;}}
}
