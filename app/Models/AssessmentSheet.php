<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class AssessmentSheet extends Model {
 protected $fillable=['study_group_id','discipline_id','teacher_id','academic_year','semester','control_type','assessment_date','number','status','closed_at','closed_by'];
 protected function casts(): array{return ['assessment_date'=>'date','closed_at'=>'datetime'];}
 public function group(): BelongsTo{return $this->belongsTo(StudyGroup::class,'study_group_id');}
 public function discipline(): BelongsTo{return $this->belongsTo(Discipline::class);}
 public function teacher(): BelongsTo{return $this->belongsTo(Teacher::class);}
 public function rows(): HasMany{return $this->hasMany(AssessmentSheetRow::class);}
}
