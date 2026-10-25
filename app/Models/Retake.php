<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Retake extends Model {
 protected $fillable=['academic_debt_id','student_id','discipline_id','teacher_id','direction_number','scheduled_at','room','status','result','grade','comment','completed_at'];
 protected function casts(): array{return ['scheduled_at'=>'datetime','completed_at'=>'datetime'];}
 public function debt(): BelongsTo{return $this->belongsTo(AcademicDebt::class,'academic_debt_id');}
 public function student(): BelongsTo{return $this->belongsTo(Student::class);}
 public function discipline(): BelongsTo{return $this->belongsTo(Discipline::class);}
 public function teacher(): BelongsTo{return $this->belongsTo(Teacher::class);}
}
