<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class JournalStudentRecord extends Model {
 protected $fillable=['journal_entry_id','student_id','attendance_status','grade','comment'];
 public function entry(): BelongsTo{return $this->belongsTo(JournalEntry::class,'journal_entry_id');}
 public function student(): BelongsTo{return $this->belongsTo(Student::class);}
}
