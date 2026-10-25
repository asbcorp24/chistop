<?php
namespace App\Filament\Resources\StudyGroups;
use App\Filament\Resources\StudyGroups\Pages; use App\Models\StudyGroup; use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker; use Filament\Forms\Components\Select; use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource; use Filament\Schemas\Schema; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Filters\SelectFilter; use Filament\Tables\Table;
class StudyGroupResource extends Resource {
 protected static ?string $model=StudyGroup::class; protected static ?string $modelLabel='учебная группа'; protected static ?string $pluralModelLabel='Учебные группы'; protected static string|\UnitEnum|null $navigationGroup='Контингент';
 public static function form(Schema $schema): Schema { return $schema->components([
  Select::make('organization_id')->label('Организация')->relationship('organization','name')->required()->searchable()->preload(),
  Select::make('specialty_id')->label('Специальность')->relationship('specialty','name')->searchable()->preload(),
  TextInput::make('name')->label('Группа')->required(), TextInput::make('admission_year')->label('Год набора')->numeric(),
  Select::make('curator_teacher_id')->label('Куратор')->relationship('curator','last_name')->searchable()->preload(),
  DatePicker::make('start_date')->label('Начало обучения'), DatePicker::make('planned_end_date')->label('Плановый выпуск'),
  Select::make('status')->label('Статус')->options(['active'=>'Обучается','graduated'=>'Выпущена','archived'=>'Архив'])->default('active')->required(),
  TextInput::make('external_id')->label('ID во внешней системе')->disabled()->dehydrated(),
 ]); }
 public static function table(Table $table): Table { return $table->columns([
  TextColumn::make('name')->label('Группа')->searchable()->sortable(), TextColumn::make('specialty.code')->label('Код специальности'),
  TextColumn::make('specialty.name')->label('Специальность')->wrap()->toggleable(), TextColumn::make('admission_year')->label('Набор')->sortable(),
  TextColumn::make('curator.full_name')->label('Куратор'), TextColumn::make('status')->label('Статус')->badge(),
  ])->filters([SelectFilter::make('status')->label('Статус')->options(['active'=>'Обучается','graduated'=>'Выпущена','archived'=>'Архив'])])->recordActions([EditAction::make()]); }
 public static function getPages(): array { return ['index'=>Pages\ListStudyGroups::route('/'),'create'=>Pages\CreateStudyGroup::route('/create'),'edit'=>Pages\EditStudyGroup::route('/{record}/edit')]; }
}
