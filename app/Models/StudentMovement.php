<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StudentMovement extends Model {
 protected $fillable=['student_id','from_group_id','to_group_id','type','effective_on','order_number','order_date','basis','created_by'];
 protected function casts(): array { return ['effective_on'=>'date','order_date'=>'date']; }
 public function student(): BelongsTo { return $this->belongsTo(Student::class); }
 public function fromGroup(): BelongsTo { return $this->belongsTo(StudyGroup::class,'from_group_id'); }
 public function toGroup(): BelongsTo { return $this->belongsTo(StudyGroup::class,'to_group_id'); }
}