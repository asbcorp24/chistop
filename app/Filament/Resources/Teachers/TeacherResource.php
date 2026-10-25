<?php
namespace App\Filament\Resources\Teachers;
use App\Filament\Resources\Teachers\Pages; use App\Models\Teacher; use Filament\Actions\EditAction;
use Filament\Forms\Components\Select; use Filament\Forms\Components\TextInput; use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource; use Filament\Schemas\Schema; use Filament\Tables\Columns\IconColumn; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Filters\SelectFilter; use Filament\Tables\Table;
class TeacherResource extends Resource {
 protected static ?string $model=Teacher::class; protected static ?string $modelLabel='преподаватель'; protected static ?string $pluralModelLabel='Преподаватели'; protected static string|\UnitEnum|null $navigationGroup='Контингент';
 public static function form(Schema $schema): Schema { return $schema->components([
  Select::make('organization_id')->label('Организация')->relationship('organization','name')->required()->searchable()->preload(),
  Select::make('department_id')->label('Подразделение')->relationship('department','name')->searchable()->preload(),
  TextInput::make('last_name')->label('Фамилия')->required(), TextInput::make('first_name')->label('Имя')->required(),
  TextInput::make('middle_name')->label('Отчество'), TextInput::make('email')->label('E-mail')->email(),
  TextInput::make('external_id')->label('ID во внешней системе')->disabled()->dehydrated(),
  Toggle::make('is_active')->label('Работает')->default(true),
 ]); }
 public static function table(Table $table): Table { return $table->columns([
  TextColumn::make('last_name')->label('Фамилия')->searchable()->sortable(), TextColumn::make('first_name')->label('Имя')->searchable(),
  TextColumn::make('middle_name')->label('Отчество')->toggleable(), TextColumn::make('department.name')->label('Подразделение')->searchable(),
  TextColumn::make('email')->label('E-mail')->searchable()->toggleable(), IconColumn::make('is_active')->label('Активен')->boolean(),
 ])->filters([SelectFilter::make('department_id')->label('Подразделение')->relationship('department','name')])->recordActions([EditAction::make()]); }
 public static function getPages(): array { return ['index'=>Pages\ListTeachers::route('/'),'create'=>Pages\CreateTeacher::route('/create'),'edit'=>Pages\EditTeacher::route('/{record}/edit')]; }
}
