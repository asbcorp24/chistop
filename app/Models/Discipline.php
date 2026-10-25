<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Discipline extends Model { protected $fillable=['organization_id','external_id','code','name','is_active']; protected function casts(): array{return ['is_active'=>'boolean'];} public function organization(): BelongsTo{return $this->belongsTo(Organization::class);} }
