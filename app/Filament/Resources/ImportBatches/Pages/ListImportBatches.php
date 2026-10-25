<?php
namespace App\Filament\Resources\ImportBatches\Pages;

use App\Filament\Pages\XmlImport;
use App\Filament\Resources\ImportBatches\ImportBatchResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListImportBatches extends ListRecords
{
    protected static string $resource = ImportBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadXml')
                ->label('Загрузить XML')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->url(XmlImport::getUrl()),
        ];
    }
}
