<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\XmlTimetableImportService;
use Illuminate\Console\Command;

class ImportTimetableXml extends Command
{
    protected $signature = 'chistop:import-timetable {file : XML file path} {--organization= : Organization ID}';
    protected $description = 'Import teachers, groups, subjects, rooms, workloads and schedule from timetable XML';

    public function handle(XmlTimetableImportService $service): int
    {
        $organizationId=(int)($this->option('organization') ?: Organization::query()->value('id'));
        if (! $organizationId) {
            $this->error('No organization found. Create an organization or pass --organization=ID.');
            return self::FAILURE;
        }

        $path=$this->argument('file');
        $this->info("Importing {$path} into organization #{$organizationId}...");
        $batch=$service->import($path,$organizationId,basename($path));
        $this->table(['Batch','Status','Created','Updated','Skipped','Errors'],[[$batch->id,$batch->status,$batch->created_count,$batch->updated_count,$batch->skipped_count,$batch->error_count]]);
        return $batch->status === 'completed' ? self::SUCCESS : self::FAILURE;
    }
}
