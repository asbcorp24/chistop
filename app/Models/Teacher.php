<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Teacher extends Model {
    protected $fillable=['organization_id','department_id','external_id','last_name','first_name','middle_name','email','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function getFullNameAttribute(): string { return trim("$this->last_name $this->first_name $this->middle_name"); }
}
