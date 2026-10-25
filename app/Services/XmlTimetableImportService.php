<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Discipline;
use App\Models\ExternalMapping;
use App\Models\ImportBatch;
use App\Models\ImportError;
use App\Models\ScheduleLesson;
use App\Models\Specialty;
use App\Models\StudyGroup;
use App\Models\StudyType;
use App\Models\Teacher;
use App\Models\TeacherWorkload;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use SimpleXMLElement;
use Throwable;

class XmlTimetableImportService
{
    private int $created = 0;
    private int $updated = 0;
    private int $skipped = 0;
    private array $teachers = [];
    private array $subjects = [];
    private array $classes = [];
    private array $rooms = [];
    private array $studyTypes = [];
    private array $loads = [];

    public function import(string $path, int $organizationId, ?string $sourceFilename = null): ImportBatch
    {
        if (! is_file($path)) {
            throw new RuntimeException("XML file not found: {$path}");
        }

        $checksum = hash_file('sha256', $path);
        $batch = ImportBatch::firstOrNew([
            'organization_id' => $organizationId,
            'type' => 'timetable_xml',
            'checksum' => $checksum,
        ]);

        if ($batch->exists && $batch->status === 'completed') {
            $batch->increment('skipped_count');
            return $batch->fresh();
        }

        $batch->fill([
            'source_filename' => $sourceFilename ?: basename($path),
            'status' => 'processing',
            'started_at' => now(),
            'finished_at' => null,
            'created_count' => 0,
            'updated_count' => 0,
            'skipped_count' => 0,
            'error_count' => 0,
        ])->save();

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($path, SimpleXMLElement::class, LIBXML_NONET | LIBXML_COMPACT);
        if ($xml === false) {
            $messages = array_map(fn ($e) => trim($e->message), libxml_get_errors());
            $batch->update(['status'=>'failed','finished_at'=>now(),'error_count'=>count($messages)]);
            foreach ($messages as $message) {
                ImportError::create(['import_batch_id'=>$batch->id,'entity_type'=>'xml','code'=>'parse_error','message'=>$message]);
            }
            throw new RuntimeException('XML parse error: '.implode('; ', array_slice($messages, 0, 3)));
        }

        try {
            DB::transaction(function () use ($xml, $organizationId, $batch) {
                $this->importSpecialties($xml, $organizationId);
                $this->importTeachers($xml, $organizationId);
                $this->importSubjects($xml, $organizationId);
                $this->importRooms($xml, $organizationId);
                $this->importStudyTypes($xml, $organizationId);
                $this->importClasses($xml, $organizationId);
                $this->importLoads($xml, $organizationId, $batch);
                $this->importSchedule($xml, $organizationId, $batch);
            });

            $batch->update([
                'status'=>'completed',
                'finished_at'=>now(),
                'created_count'=>$this->created,
                'updated_count'=>$this->updated,
                'skipped_count'=>$this->skipped,
                'meta'=>[
                    'term_begin'=>(string) $xml->term->begin_date,
                    'term_end'=>(string) $xml->term->end_date,
                    'teachers'=>count($this->teachers),
                    'subjects'=>count($this->subjects),
                    'groups'=>count($this->classes),
                    'rooms'=>count($this->rooms),
                    'loads'=>count($this->loads),
                ],
            ]);
        } catch (Throwable $e) {
            $batch->update(['status'=>'failed','finished_at'=>now(),'error_count'=>$batch->error_count + 1]);
            ImportError::create([
                'import_batch_id'=>$batch->id,
                'entity_type'=>'import',
                'code'=>'exception',
                'message'=>$e->getMessage(),
                'context'=>['class'=>$e::class],
            ]);
            throw $e;
        }

        return $batch->fresh();
    }

    private function importSpecialties(SimpleXMLElement $xml, int $org): void
    {
        foreach ($xml->specialities->speciality ?? [] as $node) {
            $id=(string)$node->id; $full=trim((string)$node->full_name);
            $parts=preg_split('/\s+/', $full, 2);
            $code=$parts[0] ?: "xml-{$id}";
            $model=Specialty::updateOrCreate(
                ['organization_id'=>$org,'code'=>$code],
                ['name'=>$full ?: trim((string)$node->short_name),'is_active'=>true]
            );
            $this->countSave($model); $this->map($org,'speciality',$id,$model);
        }
    }

    private function importTeachers(SimpleXMLElement $xml, int $org): void
    {
        foreach ($xml->teachers->teacher ?? [] as $node) {
            $p=$node->person; $id=(string)$p->id;
            $model=Teacher::updateOrCreate(
                ['organization_id'=>$org,'external_id'=>$id],
                ['last_name'=>trim((string)$p->surname) ?: '—','first_name'=>trim((string)$p->first_name) ?: '—','middle_name'=>trim((string)$p->second_name) ?: null,'is_active'=>true]
            );
            $this->countSave($model); $this->teachers[$id]=$model; $this->map($org,'teacher',$id,$model);
        }
    }

    private function importSubjects(SimpleXMLElement $xml, int $org): void
    {
        foreach ($xml->subjects->subject ?? [] as $node) {
            $id=(string)$node->id; $name=trim((string)$node->full_name) ?: trim((string)$node->short_name);
            $model=Discipline::updateOrCreate(
                ['organization_id'=>$org,'external_id'=>$id],
                ['name'=>$name ?: "Предмет {$id}",'is_active'=>true]
            );
            $this->countSave($model); $this->subjects[$id]=$model; $this->map($org,'subject',$id,$model);
        }
    }

    private function importRooms(SimpleXMLElement $xml, int $org): void
    {
        foreach ($xml->rooms->room ?? [] as $node) {
            $id=(string)$node->id;
            $model=Classroom::updateOrCreate(
                ['organization_id'=>$org,'external_id'=>$id],
                ['name'=>trim((string)$node->name) ?: "Кабинет {$id}",'capacity'=>(int)$node->capacity ?: null,'building'=>trim((string)$node->building) ?: null,'is_active'=>true]
            );
            $this->countSave($model); $this->rooms[$id]=$model; $this->map($org,'room',$id,$model);
        }
    }

    private function importStudyTypes(SimpleXMLElement $xml, int $org): void
    {
        foreach ($xml->study_types->study_type ?? [] as $node) {
            $id=(string)$node->id;
            $model=StudyType::updateOrCreate(
                ['organization_id'=>$org,'external_id'=>$id],
                ['name'=>trim((string)$node->full_name) ?: "Вид {$id}",'short_name'=>trim((string)$node->short_name) ?: null]
            );
            $this->countSave($model); $this->studyTypes[$id]=$model; $this->map($org,'study_type',$id,$model);
        }
    }

    private function importClasses(SimpleXMLElement $xml, int $org): void
    {
        foreach ($xml->classes->class ?? [] as $node) {
            $id=(string)$node->id; $specialtyId=(string)$node->speciality_id;
            $specialty=$this->mappingModelId($org,'speciality',$specialtyId);
            $model=StudyGroup::updateOrCreate(
                ['organization_id'=>$org,'external_id'=>$id],
                ['name'=>trim((string)$node->name) ?: "Группа {$id}",'specialty_id'=>$specialty,'status'=>'active']
            );
            $this->countSave($model); $this->classes[$id]=$model; $this->map($org,'class',$id,$model);
        }
    }

    private function importLoads(SimpleXMLElement $xml, int $org, ImportBatch $batch): void
    {
        $termBegin=$this->date((string)$xml->term->begin_date);
        $termEnd=$this->date((string)$xml->term->end_date);
        $academicYear=$termBegin->year.'-'.($termBegin->year + 1);

        foreach ($xml->loads->load ?? [] as $load) {
            $loadId=(string)$load->id;
            $classIds=array_map('strval', iterator_to_array($load->klass_id_list->int ?? []));
            $groups=[];
            foreach ($load->groups->group ?? [] as $index=>$group) {
                $teacherId=(string)$group->teacher_id; $subjectId=(string)$group->subject_id;
                $teacher=$this->teachers[$teacherId] ?? null; $subject=$this->subjects[$subjectId] ?? null;
                if (! $teacher || ! $subject) {
                    $this->error($batch,'load',$loadId,'missing_reference',"Нагрузка {$loadId}: не найден преподаватель {$teacherId} или предмет {$subjectId}");
                    continue;
                }
                $weekly=array_map('intval', array_map('strval', iterator_to_array($group->hour_per_week_list->int ?? [])));
                $studyTypeId=(string)$group->study_type_id;
                $groupData=['teacher'=>$teacher,'subject'=>$subject,'study_type_id'=>$studyTypeId,'node'=>$group];
                $groups[(int)$index]=$groupData;

                foreach ($classIds as $classId) {
                    $class=$this->classes[$classId] ?? null;
                    if (! $class) continue;
                    $external="{$loadId}:{$index}:{$classId}";
                    $model=TeacherWorkload::updateOrCreate(
                        ['organization_id'=>$org,'external_id'=>$external],
                        [
                            'teacher_id'=>$teacher->id,'study_group_id'=>$class->id,'discipline_id'=>$subject->id,
                            'academic_year'=>$academicYear,'semester'=>(string)($this->classSemester($xml,$classId) ?: ''),
                            'hours'=>(float)$group->hours_total,'hours_per_week'=>(float)$group->hours_per_week,
                            'week_type'=>(string)$group->week_type,'weekly_hours'=>$weekly,
                            'workload_type'=>$this->studyTypes[$studyTypeId]->name ?? null,
                            'source_data'=>['load_id'=>$loadId,'same_time'=>(string)$load->same_time,'class_id'=>$classId,'group_index'=>(int)$index,'term_begin'=>$termBegin->toDateString(),'term_end'=>$termEnd->toDateString()],
                        ]
                    );
                    $this->countSave($model); $this->map($org,'load',$external,$model);
                }
            }
            $this->loads[$loadId]=['classes'=>$classIds,'groups'=>$groups,'same_time'=>(string)$load->same_time];
        }
    }

    private function importSchedule(SimpleXMLElement $xml, int $org, ImportBatch $batch): void
    {
        $times=[];
        foreach ($xml->times->time ?? [] as $i=>$time) {
            if ((int)$i===0) continue;
            if (preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/u', (string)$time, $m)) $times[(int)$i]=[$m[1],$m[2]];
        }

        foreach ($xml->scheds->sched ?? [] as $schedIndex=>$sched) {
            $loadId=(string)$sched->load_id; $load=$this->loads[$loadId] ?? null;
            if (! $load) { $this->error($batch,'schedule',(string)$schedIndex,'missing_load',"Расписание: нагрузка {$loadId} не найдена"); continue; }
            $groupIndex=(int)$sched->group;
            $group=$load['groups'][$groupIndex] ?? reset($load['groups']);
            if (! $group) continue;
            $hour=(int)$sched->hour; $time=$times[$hour] ?? null;
            if (! $time) { $this->error($batch,'schedule',(string)$schedIndex,'missing_time',"Не найдено время пары {$hour}"); continue; }
            $begin=$this->date((string)$sched->begin_date); $end=$this->date((string)$sched->end_date);
            $weekday=(int)$sched->day;
            $roomId=(string)$sched->room_id; $room=$this->rooms[$roomId] ?? null;
            $studyTypeId=$group['study_type_id'];
            foreach ($this->datesForWeekday($begin,$end,$weekday) as $date) {
                foreach ($load['classes'] as $classId) {
                    $class=$this->classes[$classId] ?? null; if (! $class) continue;
                    $external=implode(':',[$schedIndex,$loadId,$classId,$date->format('Ymd'),$hour,$groupIndex]);
                    $model=ScheduleLesson::updateOrCreate(
                        ['organization_id'=>$org,'external_id'=>$external],
                        [
                            'study_group_id'=>$class->id,'discipline_id'=>$group['subject']->id,'teacher_id'=>$group['teacher']->id,
                            'lesson_date'=>$date->toDateString(),'starts_at'=>$time[0],'ends_at'=>$time[1],
                            'room'=>$room?->name ?: ($roomId !== '-1' ? $roomId : null),
                            'subgroup'=>$load['same_time']==='no' ? (string)($groupIndex + 1) : null,
                            'lesson_type'=>$this->studyTypes[$studyTypeId]->name ?? null,'status'=>'scheduled',
                            'source_data'=>['sched_index'=>(int)$schedIndex,'load_id'=>$loadId,'room_id'=>$roomId,'day'=>$weekday,'hour'=>$hour,'fixed'=>(string)$sched->fixed,'begin_date'=>(string)$sched->begin_date,'end_date'=>(string)$sched->end_date],
                        ]
                    );
                    $this->countSave($model);
                }
            }
        }
    }

    private function datesForWeekday(CarbonImmutable $begin, CarbonImmutable $end, int $weekday): array
    {
        if ($weekday < 1 || $weekday > 7) return [];
        $date=$begin;
        while ($date->isoWeekday() !== $weekday && $date->lte($end)) $date=$date->addDay();
        $dates=[];
        while ($date->lte($end)) { $dates[]=$date; $date=$date->addWeek(); }
        return $dates;
    }

    private function date(string $value): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('d.m.Y', trim($value))->startOfDay();
    }

    private function classSemester(SimpleXMLElement $xml, string $classId): ?int
    {
        foreach ($xml->classes->class ?? [] as $class) if ((string)$class->id===$classId) return (int)$class->semester;
        return null;
    }

    private function map(int $org, string $type, string $external, $model): void
    {
        ExternalMapping::updateOrCreate(
            ['organization_id'=>$org,'source'=>'xml_timetable','entity_type'=>$type,'external_id'=>$external],
            ['model_type'=>$model::class,'model_id'=>$model->id]
        );
    }

    private function mappingModelId(int $org, string $type, string $external): ?int
    {
        return ExternalMapping::where(['organization_id'=>$org,'source'=>'xml_timetable','entity_type'=>$type,'external_id'=>$external])->value('model_id');
    }

    private function countSave($model): void
    {
        $model->wasRecentlyCreated ? $this->created++ : ($model->wasChanged() ? $this->updated++ : $this->skipped++);
    }

    private function error(ImportBatch $batch, string $type, string $external, string $code, string $message): void
    {
        ImportError::create(['import_batch_id'=>$batch->id,'entity_type'=>$type,'external_id'=>$external,'code'=>$code,'message'=>$message]);
        $batch->increment('error_count');
    }
}
