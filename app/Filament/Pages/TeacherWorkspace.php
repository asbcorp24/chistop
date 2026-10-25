<?php
namespace App\Filament\Pages;

use App\Models\JournalEntry;
use App\Models\JournalStudentRecord;
use App\Models\ScheduleLesson;
use App\Models\Teacher;
use App\Models\TeacherWorkload;
use Filament\Pages\Page;

class TeacherWorkspace extends Page
{
    protected static string|\BackedEnum|null $navigationIcon='heroicon-o-academic-cap';
    protected static ?string $navigationLabel='Кабинет преподавателя';
    protected static string|\UnitEnum|null $navigationGroup='Рабочее пространство';
    protected static ?int $navigationSort=1;
    protected static ?string $title='Кабинет преподавателя';
    protected string $view='filament.pages.teacher-workspace';

    public ?int $teacherId=null;

    public function mount(): void
    {
        $u=auth()->user();
        $this->teacherId=$u?->teacher_id ?: Teacher::query()->where('is_active',true)->value('id');
    }

    public function getTeachersProperty(){return Teacher::query()->where('is_active',true)->orderBy('last_name')->orderBy('first_name')->get();}
    public function getTeacherProperty(){return $this->teacherId?Teacher::find($this->teacherId):null;}

    public function getStatsProperty(): array
    {
        if(!$this->teacherId)return [];
        $today=now()->toDateString();
        $lessonIds=ScheduleLesson::where('teacher_id',$this->teacherId)->whereDate('lesson_date','<=',$today)->where('status','!=','cancelled')->pluck('id');
        $entries=JournalEntry::whereIn('schedule_lesson_id',$lessonIds);
        $entryIds=(clone $entries)->pluck('id');
        $numeric=JournalStudentRecord::whereIn('journal_entry_id',$entryIds)->whereIn('grade',['2','3','4','5']);
        $records=JournalStudentRecord::whereIn('journal_entry_id',$entryIds);
        $total=(clone $records)->count();
        $present=(clone $records)->whereIn('attendance_status',['present','late'])->count();
        $plan=(float)TeacherWorkload::where('teacher_id',$this->teacherId)->sum('hours');
        $fact=ScheduleLesson::where('teacher_id',$this->teacherId)->whereDate('lesson_date','<=',$today)->where('status','!=','cancelled')->count();
        return [
            'plan'=>$plan,'fact'=>$fact,'avg'=>round((float)(clone $numeric)->avg('grade'),2),
            'attendance'=>$total?round($present*100/$total,1):0,
            'missing_topics'=>(clone $entries)->where(fn($q)=>$q->whereNull('topic')->orWhere('topic',''))->count(),
            'open_journal'=>(clone $entries)->where('is_closed',false)->count(),
        ];
    }

    public function getTodayLessonsProperty()
    {
        return $this->teacherId?ScheduleLesson::with(['group','discipline'])->where('teacher_id',$this->teacherId)->whereDate('lesson_date',today())->where('status','!=','cancelled')->orderByRaw('COALESCE(pair_number,999)')->orderBy('starts_at')->get():collect();
    }

    public function getProblemsProperty()
    {
        if(!$this->teacherId)return collect();
        return JournalEntry::with('lesson.group','lesson.discipline')->whereHas('lesson',fn($q)=>$q->where('teacher_id',$this->teacherId)->whereDate('lesson_date','<',today())->where('status','!=','cancelled'))
            ->where(fn($q)=>$q->where('is_closed',false)->orWhereNull('topic')->orWhere('topic',''))->latest('updated_at')->limit(20)->get();
    }

    public function getLoadsProperty()
    {
        if(!$this->teacherId)return collect();
        return TeacherWorkload::with(['group','discipline'])->where('teacher_id',$this->teacherId)->orderBy('academic_year')->get()->map(function($x){
            $x->fact=ScheduleLesson::where('teacher_id',$this->teacherId)->where('discipline_id',$x->discipline_id)->when($x->study_group_id,fn($q)=>$q->where('study_group_id',$x->study_group_id))->whereDate('lesson_date','<=',today())->where('status','!=','cancelled')->count();
            $x->pct=(float)$x->hours>0?round($x->fact*100/(float)$x->hours,1):null;return $x;
        });
    }

    public static function canAccess(): bool
    {
        $u=auth()->user();return (bool)($u&&($u->teacher_id||in_array($u->role,['admin','superadmin','head_academic','academic_head'],true)));
    }
}
