<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Specialty extends Model {
    protected $fillable=['organization_id','code','name','qualification','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
}
