<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CurriculumTopic extends Model {
 protected $fillable=['curriculum_plan_id','sort_order','section','title','planned_hours','planned_on','control_type','is_checkpoint','note'];
 protected function casts(): array{return ['planned_on'=>'date','is_checkpoint'=>'boolean','planned_hours'=>'decimal:2'];}
 public function plan(){return $this->belongsTo(CurriculumPlan::class,'curriculum_plan_id');}
}
