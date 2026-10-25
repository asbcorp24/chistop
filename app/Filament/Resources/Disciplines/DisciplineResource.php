<?php
namespace App\Filament\Resources\Disciplines;
use App\Filament\Resources\Disciplines\Pages; use App\Models\Discipline; use Filament\Actions\EditAction; use Filament\Forms\Components\Select; use Filament\Forms\Components\TextInput; use Filament\Forms\Components\Toggle; use Filament\Resources\Resource; use Filament\Schemas\Schema; use Filament\Tables\Columns\IconColumn; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Table;
class DisciplineResource extends Resource {
 protected static ?string $model=Discipline::class; protected static ?string $modelLabel='дисциплина'; protected static ?string $pluralModelLabel='Дисциплины'; protected static string|\\UnitEnum|null $navigationGroup='Учебная часть';
 public static function form(Schema $s): Schema{return $s->components([Select::make('organization_id')->label('Организация')->relationship('organization','name')->required()->searchable()->preload(),TextInput::make('code')->label('Код'),TextInput::make('name')->label('Наименование')->required(),TextInput::make('external_id')->label('ID XML')->disabled()->dehydrated(),Toggle::make('is_active')->label('Действующая')->default(true)]);}
 public static function table(Table $t): Table{return $t->columns([TextColumn::make('code')->label('Код')->searchable(),TextColumn::make('name')->label('Дисциплина')->searchable()->sortable()->wrap(),TextColumn::make('organization.short_name')->label('Организация'),IconColumn::make('is_active')->label('Активна')->boolean()])->recordActions([EditAction::make()]);}
 public static function getPages(): array{return ['index'=>Pages\ListDisciplines::route('/'),'create'=>Pages\CreateDiscipline::route('/create'),'edit'=>Pages\EditDiscipline::route('/{record}/edit')];}
}
