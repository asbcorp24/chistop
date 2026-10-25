<?php
namespace App\Filament\Pages;

use App\Models\ScheduleLesson;
use Carbon\Carbon;
use Filament\Pages\Page;

class MySchedule extends Page
{
    protected static string|\BackedEnum|null $navigationIcon='heroicon-o-calendar';
    protected static ?string $navigationLabel='Моё расписание';
    protected static string|\UnitEnum|null $navigationGroup='Рабочее пространство';
    protected static ?int $navigationSort=2;
    protected static ?string $title='Моё расписание';
    protected string $view='filament.pages.my-schedule';

    public string $date='';
    public string $mode='day';
    public array $days=[];
    public array $scheduleGroups=[];

    public function mount(): void {$this->date=now()->toDateString();$this->loadSchedule();}
    public function updatedDate(): void {$this->loadSchedule();}
    public function updatedMode(): void {if(!in_array($this->mode,['day','week'],true))$this->mode='day';$this->loadSchedule();}
    public function previous(): void {$d=Carbon::parse($this->date);$this->date=($this->mode==='week'?$d->subWeek():$d->subDay())->toDateString();$this->loadSchedule();}
    public function next(): void {$d=Carbon::parse($this->date);$this->date=($this->mode==='week'?$d->addWeek():$d->addDay())->toDateString();$this->loadSchedule();}
    public function today(): void {$this->date=now()->toDateString();$this->loadSchedule();}
    public function selectDay(string $date): void {$this->mode='day';$this->date=$date;$this->loadSchedule();}

    protected function loadSchedule(): void
    {
        $u=auth()->user();if(!$u){$this->scheduleGroups=[];return;}
        $date=Carbon::parse($this->date);$from=$this->mode==='week'?$date->copy()->startOfWeek():$date->copy();$to=$this->mode==='week'?$from->copy()->addDays(6):$date->copy();
        $q=ScheduleLesson::with(['group','discipline','teacher'])->whereBetween('lesson_date',[$from->toDateString(),$to->toDateString()])->where('status','!=','cancelled');
        if($u->teacher_id)$q->where('teacher_id',$u->teacher_id);
        elseif($u->student_id){
            $gid=$u->student?->enrollments()->whereNull('ended_at')->latest('started_at')->value('study_group_id');
            if(!$gid){$this->scheduleGroups=[];$this->buildDays($from,$to);return;}
            $q->where('study_group_id',$gid);
        } else {$this->scheduleGroups=[];$this->buildDays($from,$to);return;}
        $items=$q->orderBy('study_group_id')->orderBy('lesson_date')->orderByRaw('COALESCE(pair_number,999)')->orderBy('starts_at')->get();
        $this->scheduleGroups=$items->groupBy('study_group_id')->map(function($rows){
            $first=$rows->first();
            return ['id'=>$first->study_group_id,'name'=>$first->group?->name??'—','days'=>$rows->groupBy(fn($l)=>$l->lesson_date->toDateString())->map(fn($day)=>$day->map(fn($l)=>[
                'id'=>$l->id,'pair_number'=>$l->pair_number,'subgroup'=>$l->subgroup,'status'=>$l->status,'change_type'=>$l->change_type,
                'subject_name'=>$l->discipline?->name,'room'=>$l->room,'teacher'=>$l->teacher?->full_name,'starts_at'=>$l->starts_at,'ends_at'=>$l->ends_at,
            ])->all())->all()];
        })->values()->all();
        $this->buildDays($from,$to);
    }

    private function buildDays(Carbon $from,Carbon $to): void
    {
        $this->days=[];$names=['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'];$short=['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
        for($d=$from->copy();$d->lte($to);$d->addDay())$this->days[$d->toDateString()]=['short'=>$short[$d->isoWeekday()-1],'name'=>$names[$d->isoWeekday()-1],'date'=>$d->format('d.m')];
    }

    public static function canAccess(): bool
    {
        $u=auth()->user();return (bool)($u&&($u->teacher_id||$u->student_id));
    }
}
