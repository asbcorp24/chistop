<?php
namespace App\Filament\Pages;
use App\Models\HomeworkSubmission;use Filament\Notifications\Notification;use Filament\Pages\Page;
class HomeworkReview extends Page{
 protected static string|\BackedEnum|null $navigationIcon='heroicon-o-clipboard-document-check';protected static ?string $navigationLabel='Домашние задания';protected static string|\UnitEnum|null $navigationGroup='Рабочее пространство';protected static ?string $title='Ведомость домашних заданий';protected string $view='filament.pages.homework-review';
 public string $status='submitted';public array $comments=[];
 public function getItemsProperty(){return HomeworkSubmission::with(['student','lesson.group','lesson.discipline'])->whereHas('lesson',fn($q)=>$q->when(auth()->user()?->teacher_id,fn($x)=>$x->where('teacher_id',auth()->user()->teacher_id)))->where('status',$this->status)->latest('submitted_at')->get();}
 public function setStatus(string $status):void{if(in_array($status,['submitted','revision','checked'],true))$this->status=$status;}
 public function review(int $id,string $status):void{$s=HomeworkSubmission::with('lesson')->findOrFail($id);if(auth()->user()?->teacher_id&&$s->lesson->teacher_id!==auth()->user()->teacher_id)abort(403);$s->update(['status'=>$status,'teacher_comment'=>$this->comments[$id]??null,'checked_by'=>auth()->id(),'checked_at'=>now()]);Notification::make()->title($status==='checked'?'Работа принята':'Отправлено на доработку')->success()->send();}
 public static function canAccess():bool{$u=auth()->user();return (bool)($u&&($u->teacher_id||in_array($u->role,['admin','superadmin','head_academic','academic_head'],true)));}
}