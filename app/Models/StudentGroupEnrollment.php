<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StudentGroupEnrollment extends Model {
    protected $fillable=['student_id','study_group_id','started_at','ended_at','reason','order_number','order_date'];
    protected function casts(): array { return ['started_at'=>'date','ended_at'=>'date','order_date'=>'date']; }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function group(): BelongsTo { return $this->belongsTo(StudyGroup::class,'study_group_id'); }
}
