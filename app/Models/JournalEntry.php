<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class JournalEntry extends Model {
 protected $fillable=['schedule_lesson_id','topic','homework','control_type','is_closed','closed_at','closed_by'];
 protected function casts(): array{return ['is_closed'=>'boolean','closed_at'=>'datetime'];}
 public function lesson(): BelongsTo{return $this->belongsTo(ScheduleLesson::class,'schedule_lesson_id');}
 public function records(): HasMany{return $this->hasMany(JournalStudentRecord::class);}
}
