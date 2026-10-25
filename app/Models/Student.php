<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Student extends Model {
    protected $fillable=['organization_id','external_id','personal_file_number','last_name','first_name','middle_name','birth_date','status'];
    protected function casts(): array { return ['birth_date'=>'date']; }
    public function enrollments(): HasMany { return $this->hasMany(StudentGroupEnrollment::class); }
    public function getFullNameAttribute(): string { return trim("$this->last_name $this->first_name $this->middle_name"); }
}
