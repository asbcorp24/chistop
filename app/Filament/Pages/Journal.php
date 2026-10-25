<?php
namespace App\Filament\Pages;
use App\Models\JournalEntry;
use App\Models\JournalStudentRecord;
use App\Models\ScheduleLesson;
use App\Models\StudyGroup;
use App\Services\JournalRosterService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
class Journal extends Page {
 protected static string|\BackedEnum|null $navigationIcon='heroicon-o-book-open';
 protected static ?string $navigationLabel='Журнал группы';
 protected static string|\UnitEnum|null $navigationGroup='Учебная часть';
 protected static ?string $title='Электронный журнал';
 protected string $view='filament.pages.journal';
 public ?int $groupId=null; public ?int $lessonId=null; public array $records=[];
 public function mount(): void {$this->groupId=StudyGroup::query()->where('status','active')->value('id'); $this->loadLesson();}
 public function updatedGroupId(): void {$this->lessonId=null;$this->loadLesson();}
 public function updatedLessonId(): void {$this->loadLesson();}
 public function getGroupsProperty(){return StudyGroup::query()->where('status','active')->orderBy('name')->get();}
 public function getLessonsProperty(){return $this->groupId?ScheduleLesson::query()->where('study_group_id',$this->groupId)->with('discipline')->orderByDesc('lesson_date')->orderByDesc('starts_at')->limit(60)->get():collect();}
 public function loadLesson(): void {
  if(!$this->lessonId){$this->lessonId=$this->lessons->first()?->id;}
  if(!$this->lessonId){$this->records=[];return;}
  $lesson=ScheduleLesson::with(['discipline','teacher'])->find($this->lessonId); if(!$lesson)return;
  $entry=JournalEntry::firstOrCreate(['schedule_lesson_id'=>$lesson->id]);
  foreach(app(JournalRosterService::class)->studentsForGroupAt($lesson->study_group_id,$lesson->lesson_date->toDateString()) as $student){
   $r=JournalStudentRecord::firstOrCreate(['journal_entry_id'=>$entry->id,'student_id'=>$student->id],['attendance_status'=>'present']);
   $this->records[$student->id]=['name'=>$student->full_name,'attendance_status'=>$r->attendance_status,'grade'=>$r->grade,'comment'=>$r->comment];
  }
 }
 public function save(): void {
  $entry=JournalEntry::where('schedule_lesson_id',$this->lessonId)->firstOrFail();
  if($entry->is_closed){Notification::make()->title('Занятие закрыто')->danger()->send();return;}
  foreach($this->records as $studentId=>$data){JournalStudentRecord::updateOrCreate(['journal_entry_id'=>$entry->id,'student_id'=>$studentId],['attendance_status'=>$data['attendance_status']??'present','grade'=>$data['grade']?:null,'comment'=>$data['comment']?:null]);}
  Notification::make()->title('Журнал сохранён')->success()->send();
 }
}
