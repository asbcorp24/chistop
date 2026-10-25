<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CurriculumPlan extends Model {
 protected $fillable=['organization_id','study_group_id','discipline_id','teacher_id','academic_year','semester','title','version','purpose','learning_outcomes','requirements','assessment','literature','resources','status','approval_status','created_by','approved_by','approved_at'];
 protected function casts(): array{return ['approved_at'=>'datetime'];}
 public function group(){return $this->belongsTo(StudyGroup::class,'study_group_id');}
 public function discipline(){return $this->belongsTo(Discipline::class);}
 public function teacher(){return $this->belongsTo(Teacher::class);}
 public function topics(){return $this->hasMany(CurriculumTopic::class)->orderBy('sort_order');}
}
