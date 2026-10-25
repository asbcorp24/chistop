<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class CuratorWorkPlan extends Model{protected $fillable=['study_group_id','teacher_id','academic_year','title','content','status'];public function group(){return $this->belongsTo(StudyGroup::class,'study_group_id');}public function teacher(){return $this->belongsTo(Teacher::class);}}
