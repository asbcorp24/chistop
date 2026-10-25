<?php
namespace App\Filament\Resources\Organizations;

use App\Filament\Resources\Organizations\Pages;
use App\Models\Organization;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;
    protected static ?string $modelLabel = 'организация';
    protected static ?string $pluralModelLabel = 'Организации';
    protected static ?string $navigationLabel = 'Организации';
    protected static string|\UnitEnum|null $navigationGroup = 'Структура колледжа';

    public static function form(Schema $schema): Schema { return $schema->components([
        TextInput::make('name')->label('Полное наименование')->required()->maxLength(255),
        TextInput::make('short_name')->label('Краткое наименование')->maxLength(255),
        TextInput::make('inn')->label('ИНН')->maxLength(12),
        Toggle::make('is_active')->label('Действующая')->default(true),
    ]); }
    public static function table(Table $table): Table { return $table->columns([
        TextColumn::make('name')->label('Организация')->searchable()->sortable(),
        TextColumn::make('short_name')->label('Краткое название')->searchable(),
        TextColumn::make('inn')->label('ИНН'),
        IconColumn::make('is_active')->label('Активна')->boolean(),
    ])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]); }
    public static function getPages(): array { return [
        'index'=>Pages\ListOrganizations::route('/'),
        'create'=>Pages\CreateOrganization::route('/create'),
        'edit'=>Pages\EditOrganization::route('/{record}/edit'),
    ]; }
}
