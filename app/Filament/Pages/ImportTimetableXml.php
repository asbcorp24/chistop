<?php

namespace App\Filament\Pages;

use App\Models\Organization;
use App\Services\XmlTimetableImportService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Throwable;

class ImportTimetableXml extends Page
{
    use WithFileUploads;

    protected string $view = 'filament.pages.import-timetable-xml';
    protected static ?string $title = 'Импорт расписания XML';
    protected static ?string $navigationLabel = 'Загрузка XML';
    protected static string|\UnitEnum|null $navigationGroup = 'Интеграции';
    protected static ?int $navigationSort = 1;

    public $xmlFile = null;
    public ?int $organizationId = null;
    public ?array $lastResult = null;

    public function mount(): void
    {
        $this->organizationId = Organization::query()->value('id');
    }

    public function getOrganizationsProperty(): array
    {
        return Organization::query()->orderBy('name')->pluck('name','id')->all();
    }

    public function import(XmlTimetableImportService $service): void
    {
        $this->validate([
            'organizationId' => ['required','exists:organizations,id'],
            'xmlFile' => ['required','file','max:30720'],
        ]);

        try {
            $batch=$service->import(
                $this->xmlFile->getRealPath(),
                $this->organizationId,
                $this->xmlFile->getClientOriginalName()
            );
            $this->lastResult=[
                'id'=>$batch->id,'status'=>$batch->status,'created'=>$batch->created_count,
                'updated'=>$batch->updated_count,'skipped'=>$batch->skipped_count,'errors'=>$batch->error_count,
            ];
            $this->reset('xmlFile');
            Notification::make()->title('XML импортирован')->body("Создано: {$batch->created_count}; обновлено: {$batch->updated_count}; ошибок: {$batch->error_count}")->success()->send();
        } catch (Throwable $e) {
            Notification::make()->title('Ошибка импорта')->body($e->getMessage())->danger()->persistent()->send();
        }
    }
}
