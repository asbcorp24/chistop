<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class StudentCharacteristic extends Model{protected $fillable=['student_id','teacher_id','request_basis','content','prepared_on'];protected function casts():array{return ['prepared_on'=>'date'];}public function student(){return $this->belongsTo(Student::class);}public function teacher(){return $this->belongsTo(Teacher::class);}}
