<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StudyGroup extends Model {
    protected $fillable=['organization_id','specialty_id','curator_teacher_id','external_id','name','admission_year','start_date','planned_end_date','status'];
    protected function casts(): array { return ['start_date'=>'date','planned_end_date'=>'date']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function specialty(): BelongsTo { return $this->belongsTo(Specialty::class); }
    public function curator(): BelongsTo { return $this->belongsTo(Teacher::class,'curator_teacher_id'); }
}
