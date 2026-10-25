<?php
use Illuminate\Support\Facades\Artisan;

Artisan::command('chistop:about', function () {
    $this->info('Chistop — информационная система колледжа');
})->purpose('Show Chistop information');
