<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('source_filename')->nullable();
            $table->string('checksum', 64)->index();
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('error_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'type', 'checksum']);
        });

        Schema::create('import_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type')->nullable();
            $table->string('external_id')->nullable();
            $table->string('code')->nullable();
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('external_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('source')->default('xml');
            $table->string('entity_type');
            $table->string('external_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->timestamps();
            $table->unique(['organization_id', 'source', 'entity_type', 'external_id'], 'external_mapping_unique');
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_mappings');
        Schema::dropIfExists('import_errors');
        Schema::dropIfExists('import_batches');
    }
};
