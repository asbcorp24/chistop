<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Classroom extends Model {
    protected $fillable=['organization_id','external_id','name','capacity','building','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
}
