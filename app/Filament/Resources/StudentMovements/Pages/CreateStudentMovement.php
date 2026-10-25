<?php
namespace App\Filament\Resources\StudentMovements\Pages;
use App\Filament\Resources\StudentMovements\StudentMovementResource;
use App\Models\{Student,StudentGroupEnrollment,StudentMovement};
use Filament\Forms\Components\{DatePicker,Select,Textarea,TextInput};
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class CreateStudentMovement extends CreateRecord {
 protected static string $resource=StudentMovementResource::class;
 public function form(Schema $schema): Schema { return $schema->components([
  Select::make('student_id')->label('Студент')->relationship('student','last_name')->getOptionLabelFromRecordUsing(fn(Student $s)=>$s->full_name)->searchable(['last_name','first_name','middle_name'])->preload()->required(),
  Select::make('type')->label('Операция')->options(['enrollment'=>'Зачисление','transfer'=>'Перевод','academic_leave'=>'Академический отпуск','return_from_leave'=>'Возврат из отпуска','expulsion'=>'Отчисление','graduation'=>'Выпуск','reinstatement'=>'Восстановление'])->required()->live(),
  Select::make('to_group_id')->label('Новая группа')->relationship('toGroup','name')->searchable()->preload()->visible(fn($get)=>in_array($get('type'),['enrollment','transfer','return_from_leave','reinstatement'],true))->required(fn($get)=>in_array($get('type'),['enrollment','transfer','return_from_leave','reinstatement'],true)),
  DatePicker::make('effective_on')->label('Дата вступления в силу')->default(today())->required(),
  TextInput::make('order_number')->label('№ приказа')->required(), DatePicker::make('order_date')->label('Дата приказа')->required(),
  Textarea::make('basis')->label('Основание')->rows(3),
 ]); }
 protected function handleRecordCreation(array $data): Model {
  return DB::transaction(function() use($data) {
   $student=Student::lockForUpdate()->findOrFail($data['student_id']);
   $current=StudentGroupEnrollment::where('student_id',$student->id)->whereNull('ended_at')->latest('started_at')->first();
   $needsGroup=in_array($data['type'],['enrollment','transfer','return_from_leave','reinstatement'],true);
   $movement=StudentMovement::create($data+['from_group_id'=>$current?->study_group_id,'to_group_id'=>$needsGroup?($data['to_group_id']??null):null,'created_by'=>auth()->id()]);
   if($current && ($data['type']==='transfer' || !$needsGroup)) $current->update(['ended_at'=>$data['effective_on'],'reason'=>$data['basis']??$data['type'],'order_number'=>$data['order_number'],'order_date'=>$data['order_date']]);
   if($needsGroup && (!$current || $current->study_group_id!=(int)$data['to_group_id'])) StudentGroupEnrollment::create(['student_id'=>$student->id,'study_group_id'=>$data['to_group_id'],'started_at'=>$data['effective_on'],'reason'=>$data['basis']??$data['type'],'order_number'=>$data['order_number'],'order_date'=>$data['order_date']]);
   $student->update(['status'=>match($data['type']){'academic_leave'=>'academic_leave','expulsion'=>'expelled','graduation'=>'graduated',default=>'active'}]);
   return $movement;
  });
 }
 protected function getRedirectUrl(): string { return static::getResource()::getUrl('index'); }
}