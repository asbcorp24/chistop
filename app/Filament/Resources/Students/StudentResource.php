<?php
namespace App\Filament\Resources\Students;
use App\Filament\Resources\Students\Pages; use App\Models\Student; use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker; use Filament\Forms\Components\Select; use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource; use Filament\Schemas\Schema; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Filters\SelectFilter; use Filament\Tables\Table;
class StudentResource extends Resource {
 protected static ?string $model=Student::class; protected static ?string $modelLabel='студент'; protected static ?string $pluralModelLabel='Студенты'; protected static string|\\UnitEnum|null $navigationGroup='Контингент';
 public static function form(Schema $schema): Schema { return $schema->components([
  Select::make('organization_id')->label('Организация')->relationship('organization','name')->required()->searchable()->preload(),
  TextInput::make('personal_file_number')->label('№ личного дела'), TextInput::make('last_name')->label('Фамилия')->required(),
  TextInput::make('first_name')->label('Имя')->required(), TextInput::make('middle_name')->label('Отчество'),
  DatePicker::make('birth_date')->label('Дата рождения'),
  Select::make('status')->label('Статус')->options(['active'=>'Обучается','academic_leave'=>'Академический отпуск','expelled'=>'Отчислен','graduated'=>'Выпускник'])->default('active')->required(),
  TextInput::make('external_id')->label('ID во внешней системе')->disabled()->dehydrated(),
 ]); }
 public static function table(Table $table): Table { return $table->columns([
  TextColumn::make('personal_file_number')->label('Личное дело')->searchable()->toggleable(),
  TextColumn::make('last_name')->label('Фамилия')->searchable()->sortable(), TextColumn::make('first_name')->label('Имя')->searchable(),
  TextColumn::make('middle_name')->label('Отчество')->searchable()->toggleable(), TextColumn::make('birth_date')->label('Дата рождения')->date('d.m.Y')->sortable(),
  TextColumn::make('status')->label('Статус')->badge(),
  ])->filters([SelectFilter::make('status')->label('Статус')->options(['active'=>'Обучается','academic_leave'=>'Академический отпуск','expelled'=>'Отчислен','graduated'=>'Выпускник'])])->recordActions([EditAction::make()]); }
 public static function getPages(): array { return ['index'=>Pages\ListStudents::route('/'),'create'=>Pages\CreateStudent::route('/create'),'edit'=>Pages\EditStudent::route('/{record}/edit')]; }
}
