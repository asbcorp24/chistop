<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HomeworkSubmission extends Model {
 protected $fillable=['schedule_lesson_id','student_id','answer_text','file_path','original_name','status','teacher_comment','checked_by','submitted_at','checked_at'];
 protected function casts(): array{return ['submitted_at'=>'datetime','checked_at'=>'datetime'];}
 public function lesson(){return $this->belongsTo(ScheduleLesson::class,'schedule_lesson_id');}
 public function student(){return $this->belongsTo(Student::class);}
}
