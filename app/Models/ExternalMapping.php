<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ExternalMapping extends Model {
 protected $fillable=['organization_id','source','entity_type','external_id','model_type','model_id'];
 public function model() { return $this->morphTo(); }
}
