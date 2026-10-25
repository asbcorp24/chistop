<?php
namespace App\Filament\Resources\TeacherWorkloads;
use App\Filament\Resources\TeacherWorkloads\Pages; use App\Models\TeacherWorkload; use Filament\Resources\Resource; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Filters\SelectFilter; use Filament\Tables\Table;
class TeacherWorkloadResource extends Resource {
 protected static ?string $model=TeacherWorkload::class; protected static ?string $modelLabel='нагрузка'; protected static ?string $pluralModelLabel='Педагогическая нагрузка'; protected static ?string $navigationGroup='Учебная часть';
 public static function table(Table $t): Table{return $t->columns([TextColumn::make('teacher.full_name')->label('Преподаватель')->searchable(),TextColumn::make('group.name')->label('Группа'),TextColumn::make('discipline.name')->label('Дисциплина')->wrap(),TextColumn::make('academic_year')->label('Учебный год')->sortable(),TextColumn::make('semester')->label('Семестр'),TextColumn::make('workload_type')->label('Вид нагрузки'),TextColumn::make('hours')->label('Часы')->numeric(2)->sortable()])->filters([SelectFilter::make('teacher_id')->label('Преподаватель')->relationship('teacher','last_name'),SelectFilter::make('study_group_id')->label('Группа')->relationship('group','name')]);}
 public static function canCreate(): bool{return false;} public static function getPages(): array{return ['index'=>Pages\ListTeacherWorkloads::route('/')];}
}
