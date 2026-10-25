<?php
namespace App\Filament\Resources\Specialties;
use App\Filament\Resources\Specialties\Pages;
use App\Models\Specialty;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
class SpecialtyResource extends Resource {
 protected static ?string $model=Specialty::class;
 protected static ?string $modelLabel='специальность';
 protected static ?string $pluralModelLabel='Специальности';
 protected static string|\\UnitEnum|null $navigationGroup='Структура колледжа';
 public static function form(Schema $schema): Schema { return $schema->components([
  Select::make('organization_id')->label('Организация')->relationship('organization','name')->required()->searchable()->preload(),
  TextInput::make('code')->label('Код')->required(), TextInput::make('name')->label('Наименование')->required(),
  TextInput::make('qualification')->label('Квалификация'), Toggle::make('is_active')->label('Действующая')->default(true),
 ]); }
 public static function table(Table $table): Table { return $table->columns([
  TextColumn::make('code')->label('Код')->searchable()->sortable(), TextColumn::make('name')->label('Специальность')->searchable()->wrap(),
  TextColumn::make('qualification')->label('Квалификация'), TextColumn::make('organization.short_name')->label('Организация'),
  ])->recordActions([EditAction::make()]); }
 public static function getPages(): array { return ['index'=>Pages\ListSpecialties::route('/'),'create'=>Pages\CreateSpecialty::route('/create'),'edit'=>Pages\EditSpecialty::route('/{record}/edit')]; }
}
