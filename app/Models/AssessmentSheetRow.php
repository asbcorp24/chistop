<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AssessmentSheetRow extends Model {
 protected $fillable=['assessment_sheet_id','student_id','result','grade','comment'];
 public function sheet(): BelongsTo{return $this->belongsTo(AssessmentSheet::class,'assessment_sheet_id');}
 public function student(): BelongsTo{return $this->belongsTo(Student::class);}
}
