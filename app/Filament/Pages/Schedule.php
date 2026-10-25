<?php
namespace App\Filament\Pages;
use App\Models\ScheduleLesson; use App\Models\StudyGroup; use App\Models\Teacher; use Carbon\Carbon; use Filament\Pages\Page;
class Schedule extends Page {
 protected static string|\BackedEnum|null $navigationIcon='heroicon-o-calendar-days'; protected static ?string $navigationLabel='Расписание занятий'; protected static string|\UnitEnum|null $navigationGroup='Учебная часть'; protected static ?string $title='Расписание занятий'; protected static ?int $navigationSort=1; protected string $view='filament.pages.schedule';
 public string $mode='week',$date='',$scope='group'; public ?int $groupId=null,$teacherId=null;
 public function mount(): void {$this->date=now()->toDateString();}
 public function previous(): void {$this->date=Carbon::parse($this->date)->subDays($this->mode==='week'?7:1)->toDateString();} public function next(): void {$this->date=Carbon::parse($this->date)->addDays($this->mode==='week'?7:1)->toDateString();} public function today(): void {$this->date=now()->toDateString();} public function selectDate(string $d): void {$this->date=$d;}
 public function getGroupsProperty(){return StudyGroup::where('is_active',true)->orderBy('name')->get();} public function getTeachersProperty(){return Teacher::where('is_active',true)->orderBy('last_name')->orderBy('first_name')->get();}
 public function getDaysProperty(){$d=Carbon::parse($this->date);$s=$this->mode==='week'?$d->copy()->startOfWeek():$d;return collect(range(0,$this->mode==='week'?6:0))->map(fn($i)=>$s->copy()->addDays($i));}
 public function lessonsFor(string $date){return ScheduleLesson::with(['group','discipline','teacher'])->whereDate('lesson_date',$date)->when($this->scope==='group'&&$this->groupId,fn($q)=>$q->where('study_group_id',$this->groupId))->when($this->scope==='teacher'&&$this->teacherId,fn($q)=>$q->where('teacher_id',$this->teacherId))->orderByRaw('COALESCE(pair_number, 999)')->orderBy('starts_at')->get();}
}
