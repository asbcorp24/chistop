<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Department extends Model {
    protected $fillable=['organization_id','parent_id','name','code','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class,'parent_id'); }
}
