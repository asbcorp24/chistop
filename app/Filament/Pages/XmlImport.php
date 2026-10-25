<?php
namespace App\Filament\Pages;

use App\Models\ImportBatch;
use App\Models\ImportError;
use App\Models\Organization;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class XmlImport extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Загрузить XML';
    protected static string|\UnitEnum|null $navigationGroup = 'Интеграции';
    protected static ?string $title = 'Загрузка XML';
    protected string $view = 'filament.pages.xml-import';

    public ?int $organizationId = null;
    public $xmlFile = null;

    public function mount(): void
    {
        $this->organizationId = Organization::query()->orderBy('name')->value('id');
    }

    public function getOrganizationsProperty()
    {
        return Organization::query()->orderBy('name')->get();
    }

    public function import(): void
    {
        $this->validate([
            'organizationId' => ['required', 'exists:organizations,id'],
            'xmlFile' => ['required', 'file', 'mimes:xml,text/xml,application/xml', 'max:20480'],
        ]);

        $realPath = $this->xmlFile->getRealPath();
        $contents = file_get_contents($realPath);
        $checksum = hash('sha256', $contents);

        $duplicate = ImportBatch::query()
            ->where('organization_id', $this->organizationId)
            ->where('type', 'timetable_xml')
            ->where('checksum', $checksum)
            ->first();

        if ($duplicate) {
            Notification::make()->title('Этот XML уже загружался')->body('Запуск №'.$duplicate->id.' · '.$duplicate->status)->warning()->send();
            return;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NONET | LIBXML_COMPACT);
        if ($xml === false || $xml->getName() !== 'timetable') {
            $messages = array_map(fn ($e) => trim($e->message), libxml_get_errors());
            libxml_clear_errors();
            Notification::make()->title('Некорректный XML')->body(implode('; ', array_slice($messages, 0, 3)) ?: 'Ожидается корневой элемент timetable')->danger()->send();
            return;
        }

        $filename = $this->xmlFile->getClientOriginalName();
        $stored = $this->xmlFile->storeAs('imports/xml', now()->format('Ymd_His').'_'.preg_replace('/[^\pL\pN._-]+/u', '_', $filename), 'local');

        $batch = ImportBatch::create([
            'organization_id' => $this->organizationId,
            'type' => 'timetable_xml',
            'source_filename' => $filename,
            'checksum' => $checksum,
            'status' => 'pending',
            'meta' => [
                'stored_path' => $stored,
                'root' => $xml->getName(),
                'teachers' => isset($xml->teachers) ? count($xml->teachers->teacher) : 0,
                'classes' => isset($xml->classes) ? count($xml->classes->klass) : 0,
                'subjects' => isset($xml->subjects) ? count($xml->subjects->subject) : 0,
                'loads' => isset($xml->loads) ? count($xml->loads->load) : 0,
                'scheds' => isset($xml->scheds) ? count($xml->scheds->sched) : 0,
            ],
        ]);

        Notification::make()->title('XML принят')->body('Создан запуск импорта №'.$batch->id.'. Файл проверен и поставлен в очередь обработки.')->success()->send();
        $this->xmlFile = null;
        $this->redirect('/admin/import-batches');
    }
}
