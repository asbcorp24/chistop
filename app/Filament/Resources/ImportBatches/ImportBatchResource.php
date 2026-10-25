<?php
namespace App\Filament\Resources\ImportBatches;
use App\Filament\Resources\ImportBatches\Pages; use App\Models\ImportBatch; use Filament\Resources\Resource; use Filament\Tables\Columns\TextColumn; use Filament\Tables\Filters\SelectFilter; use Filament\Tables\Table;
class ImportBatchResource extends Resource {
 protected static ?string $model=ImportBatch::class; protected static ?string $modelLabel='импорт'; protected static ?string $pluralModelLabel='Импорт XML'; protected static string|\\UnitEnum|null $navigationGroup='Интеграции';
 public static function table(Table $t): Table{return $t->columns([TextColumn::make('created_at')->label('Запуск')->dateTime('d.m.Y H:i')->sortable(),TextColumn::make('type')->label('Тип')->badge(),TextColumn::make('source_filename')->label('Файл')->searchable(),TextColumn::make('status')->label('Статус')->badge(),TextColumn::make('created_count')->label('Создано')->numeric(),TextColumn::make('updated_count')->label('Обновлено')->numeric(),TextColumn::make('skipped_count')->label('Пропущено')->numeric(),TextColumn::make('error_count')->label('Ошибок')->numeric()])->filters([SelectFilter::make('status')->label('Статус')->options(['pending'=>'Ожидает','processing'=>'Обрабатывается','completed'=>'Завершён','failed'=>'Ошибка'])])->defaultSort('created_at','desc');}
 public static function canCreate(): bool{return false;} public static function getPages(): array{return ['index'=>Pages\ListImportBatches::route('/')];}
}
