<?php
namespace App\Services;
use App\Models\{CurriculumPlan,ScheduleLesson};
class CurriculumScheduleBinder{
 public function bind(int $planId,bool $rebuild=false):array{
  $p=CurriculumPlan::with('topics')->find($planId);if(!$p)return ['bound'=>0,'unassigned'=>0];
  $base=ScheduleLesson::where('study_group_id',$p->study_group_id)->where('discipline_id',$p->discipline_id)->when($p->teacher_id,fn($q)=>$q->where('teacher_id',$p->teacher_id))->where('status','!=','cancelled');
  if($rebuild)(clone $base)->where('curriculum_auto_bound',true)->whereDate('lesson_date','>',today())->update(['curriculum_topic_id'=>null,'curriculum_auto_bound'=>false]);
  $lessons=(clone $base)->orderBy('lesson_date')->orderByRaw('COALESCE(pair_number,999)')->orderBy('starts_at')->get();
  $queue=[];foreach($p->topics as $t){$slots=max(1,(int)ceil((float)$t->planned_hours));for($i=0;$i<$slots;$i++)$queue[]=$t->id;}
  $bound=0;$i=0;foreach($lessons as $l){if($l->curriculum_topic_id){$i++;continue;}if(!isset($queue[$i]))break;$l->update(['curriculum_topic_id'=>$queue[$i],'curriculum_auto_bound'=>true]);$bound++;$i++;}
  return ['bound'=>$bound,'unassigned'=>max(0,count($queue)-$i),'lessons'=>$lessons->count(),'topics'=>$p->topics->count()];
 }
}
