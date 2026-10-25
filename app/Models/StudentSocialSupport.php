<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class StudentSocialSupport extends Model{protected $fillable=['student_id','support_type','basis','starts_on','ends_on','status','note','created_by'];protected function casts():array{return ['starts_on'=>'date','ends_on'=>'date'];}public function student(){return $this->belongsTo(Student::class);}}
