<?php
namespace App\Filament\Resources\ScheduleLessons;
use App\Filament\Resources\ScheduleLessons\Pages; use App\Models\ScheduleLesson; use Filament\Resources\Resource; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Filters\SelectFilter; use Filament\Tables\Table;
class ScheduleLessonResource extends Resource {
 protected static ?string $model=ScheduleLesson::class; protected static ?string $modelLabel='занятие'; protected static ?string $pluralModelLabel='Расписание'; protected static string|\UnitEnum|null $navigationGroup='Учебная часть';
 public static function table(Table $t): Table{return $t->columns([
 TextColumn::make('lesson_date')->label('Дата')->date('d.m.Y')->sortable(),TextColumn::make('starts_at')->label('Начало'),TextColumn::make('ends_at')->label('Окончание'),
 TextColumn::make('group.name')->label('Группа')->searchable(),TextColumn::make('discipline.name')->label('Дисциплина')->searchable()->wrap(),TextColumn::make('teacher.full_name')->label('Преподаватель'),TextColumn::make('room')->label('Кабинет'),TextColumn::make('lesson_type')->label('Вид занятия')->toggleable()
 ])->filters([SelectFilter::make('study_group_id')->label('Группа')->relationship('group','name'),SelectFilter::make('teacher_id')->label('Преподаватель')->relationship('teacher','last_name')])->defaultSort('lesson_date');}
 public static function shouldRegisterNavigation(): bool{return false;} public static function canCreate(): bool{return false;} public static function getPages(): array{return ['index'=>Pages\ListScheduleLessons::route('/')];}
}
