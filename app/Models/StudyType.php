<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudyType extends Model {
    protected $fillable=['organization_id','external_id','name','short_name'];
}
