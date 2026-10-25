<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ImportBatch extends Model {
 protected $fillable=['organization_id','type','source_filename','checksum','status','created_count','updated_count','skipped_count','error_count','started_at','finished_at','meta'];
 protected function casts(): array { return ['started_at'=>'datetime','finished_at'=>'datetime','meta'=>'array']; }
 public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
 public function errors(): HasMany { return $this->hasMany(ImportError::class); }
}
