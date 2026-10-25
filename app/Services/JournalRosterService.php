<?php
namespace App\Services;
use App\Models\Student;
use App\Models\StudentGroupEnrollment;
use Illuminate\Support\Collection;
class JournalRosterService {
 public function studentsForGroupAt(int $groupId, string $date): Collection {
  $ids=StudentGroupEnrollment::query()->where('study_group_id',$groupId)->whereDate('started_at','<=',$date)
   ->where(fn($q)=>$q->whereNull('ended_at')->orWhereDate('ended_at','>=',$date))->pluck('student_id');
  return Student::query()->whereIn('id',$ids)->orderBy('last_name')->orderBy('first_name')->get();
 }
}
