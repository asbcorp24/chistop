<?php
namespace App\Filament\Resources\Departments;
use App\Filament\Resources\Departments\Pages;
use App\Models\Department;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
class DepartmentResource extends Resource {
 protected static ?string $model=Department::class;
 protected static ?string $modelLabel='подразделение'; protected static ?string $pluralModelLabel='Подразделения';
 protected static ?string $navigationGroup='Структура колледжа';
 public static function form(Schema $schema): Schema { return $schema->components([
  Select::make('organization_id')->label('Организация')->relationship('organization','name')->required()->searchable()->preload(),
  Select::make('parent_id')->label('Родительское подразделение')->relationship('parent','name')->searchable()->preload(),
  TextInput::make('name')->label('Наименование')->required(), TextInput::make('code')->label('Код'),
  Toggle::make('is_active')->label('Действующее')->default(true),
 ]); }
 public static function table(Table $table): Table { return $table->columns([
  TextColumn::make('name')->label('Подразделение')->searchable()->sortable(),
  TextColumn::make('code')->label('Код')->searchable(), TextColumn::make('parent.name')->label('В составе'),
  TextColumn::make('organization.short_name')->label('Организация'), IconColumn::make('is_active')->label('Активно')->boolean(),
 ])->filters([SelectFilter::make('organization_id')->label('Организация')->relationship('organization','name')])->recordActions([EditAction::make()]); }
 public static function getPages(): array { return ['index'=>Pages\ListDepartments::route('/'),'create'=>Pages\CreateDepartment::route('/create'),'edit'=>Pages\EditDepartment::route('/{record}/edit')]; }
}
