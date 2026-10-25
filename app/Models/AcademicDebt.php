<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AcademicDebt extends Model {
 protected $fillable=['student_id','discipline_id','journal_student_record_id','academic_year','semester','reason','status','due_date','resolved_at'];
 protected function casts(): array{return ['due_date'=>'date','resolved_at'=>'datetime'];}
 public function student(): BelongsTo{return $this->belongsTo(Student::class);}
 public function discipline(): BelongsTo{return $this->belongsTo(Discipline::class);}
}
