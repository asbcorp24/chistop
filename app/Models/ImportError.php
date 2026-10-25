<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ImportError extends Model {
 protected $fillable=['import_batch_id','entity_type','external_id','code','message','context'];
 protected function casts(): array { return ['context'=>'array']; }
 public function batch(): BelongsTo { return $this->belongsTo(ImportBatch::class,'import_batch_id'); }
}
