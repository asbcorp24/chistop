<?php
namespace App\Filament\Resources\StudentMovements;
use App\Filament\Resources\StudentMovements\Pages;
use App\Models\StudentMovement;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
class StudentMovementResource extends Resource {
 protected static ?string $model=StudentMovement::class;
 protected static ?string $modelLabel='движение студента'; protected static ?string $pluralModelLabel='Движение студентов';
 protected static string|\UnitEnum|null $navigationGroup='Контингент'; protected static ?string $navigationLabel='Движение студентов';
 protected static string|\BackedEnum|null $navigationIcon='heroicon-o-arrows-right-left';
 public static function table(Table $table): Table { return $table->columns([
  TextColumn::make('effective_on')->label('Дата')->date('d.m.Y')->sortable(),
  TextColumn::make('student.full_name')->label('Студент')->searchable(['last_name','first_name','middle_name']),
  TextColumn::make('type')->label('Операция')->formatStateUsing(fn($s)=>match($s){'enrollment'=>'Зачисление','transfer'=>'Перевод','academic_leave'=>'Академ. отпуск','return_from_leave'=>'Возврат из отпуска','expulsion'=>'Отчисление','graduation'=>'Выпуск','reinstatement'=>'Восстановление',default=>$s})->badge(),
  TextColumn::make('fromGroup.name')->label('Из группы')->placeholder('—'), TextColumn::make('toGroup.name')->label('В группу')->placeholder('—'),
  TextColumn::make('order_number')->label('Приказ')->placeholder('—'),
 ])->filters([SelectFilter::make('type')->label('Операция')->options(['enrollment'=>'Зачисление','transfer'=>'Перевод','academic_leave'=>'Академический отпуск','return_from_leave'=>'Возврат из отпуска','expulsion'=>'Отчисление','graduation'=>'Выпуск','reinstatement'=>'Восстановление'])])->defaultSort('effective_on','desc'); }
 public static function getPages(): array { return ['index'=>Pages\ListStudentMovements::route('/'),'create'=>Pages\CreateStudentMovement::route('/create')]; }
}