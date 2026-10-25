<?php
namespace App\Filament\Pages;
use App\Models\{CuratorWorkPlan,StudentCharacteristic,StudentGroupEnrollment,StudyGroup,Student};
use Filament\Notifications\Notification;use Filament\Pages\Page;
class CuratorWorkspace extends Page{
 protected static string|\BackedEnum|null $navigationIcon='heroicon-o-user-group';protected static ?string $navigationLabel='Кураторство';protected static string|\UnitEnum|null $navigationGroup='Рабочее пространство';protected static ?int $navigationSort=3;protected static ?string $title='Кабинет куратора';protected string $view='filament.pages.curator-workspace';
 public ?int $groupId=null,$studentId=null;public string $academicYear='',$planTitle='План воспитательной работы',$planContent='',$planStatus='draft',$characteristicBasis='',$characteristicContent='',$preparedOn='';
 public function mount():void{$this->academicYear=now()->month>=8?now()->year.'/'.(now()->year+1):(now()->year-1).'/'.now()->year;$this->preparedOn=today()->toDateString();$this->groupId=$this->groups->first()?->id;$this->loadPlan();}
 public function getGroupsProperty(){return StudyGroup::query()->where('status','active')->when(auth()->user()?->teacher_id,fn($q)=>$q->where('curator_teacher_id',auth()->user()->teacher_id))->orderBy('name')->get();}
 public function updatedGroupId():void{$this->studentId=null;$this->loadPlan();}
 public function getStudentsProperty(){if(!$this->groupId)return collect();$ids=StudentGroupEnrollment::where('study_group_id',$this->groupId)->whereNull('ended_at')->pluck('student_id');return Student::whereIn('id',$ids)->where('status','active')->orderBy('last_name')->orderBy('first_name')->get();}
 public function getCharacteristicsProperty(){return $this->studentId?StudentCharacteristic::where('student_id',$this->studentId)->latest('prepared_on')->get():collect();}
 private function teacherId():?int{return auth()->user()?->teacher_id?:StudyGroup::find($this->groupId)?->curator_teacher_id;}
 public function loadPlan():void{$tid=$this->teacherId();$p=$this->groupId&&$tid?CuratorWorkPlan::where(['study_group_id'=>$this->groupId,'teacher_id'=>$tid,'academic_year'=>$this->academicYear])->first():null;$this->planTitle=$p?->title??'План воспитательной работы';$this->planContent=$p?->content??'';$this->planStatus=$p?->status??'draft';}
 public function savePlan():void{$tid=$this->teacherId();if(!$this->groupId||!$tid)return;$this->validate(['planTitle'=>'required|string|max:255','planStatus'=>'required|in:draft,active,completed']);CuratorWorkPlan::updateOrCreate(['study_group_id'=>$this->groupId,'teacher_id'=>$tid,'academic_year'=>$this->academicYear],['title'=>$this->planTitle,'content'=>$this->planContent?:null,'status'=>$this->planStatus]);Notification::make()->title('План воспитательной работы сохранён')->success()->send();}
 public function selectStudent(int $id):void{abort_unless($this->students->contains('id',$id),404);$this->studentId=$id;}
 public function saveCharacteristic():void{$tid=$this->teacherId();abort_unless($this->studentId&&$tid,404);$this->validate(['characteristicContent'=>'required|string|max:50000','preparedOn'=>'required|date']);StudentCharacteristic::create(['student_id'=>$this->studentId,'teacher_id'=>$tid,'request_basis'=>$this->characteristicBasis?:null,'content'=>$this->characteristicContent,'prepared_on'=>$this->preparedOn]);$this->characteristicBasis=$this->characteristicContent='';Notification::make()->title('Характеристика сохранена')->success()->send();}
 public static function canAccess():bool{$u=auth()->user();return (bool)($u&&($u->teacher_id||in_array($u->role,['admin','superadmin','head_academic','academic_head'],true)));}
}
