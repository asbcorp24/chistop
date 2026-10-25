<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('external_id');
            $table->string('name');
            $table->unsignedInteger('capacity')->nullable();
            $table->string('building')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['organization_id', 'external_id']);
        });

        Schema::create('study_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('external_id');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'external_id']);
        });

        Schema::table('teacher_workloads', function (Blueprint $table) {
            $table->decimal('hours_per_week', 8, 2)->nullable()->after('hours');
            $table->string('week_type')->nullable()->after('hours_per_week');
            $table->json('weekly_hours')->nullable()->after('week_type');
        });
    }

    public function down(): void
    {
        Schema::table('teacher_workloads', function (Blueprint $table) {
            $table->dropColumn(['hours_per_week', 'week_type', 'weekly_hours']);
        });
        Schema::dropIfExists('study_types');
        Schema::dropIfExists('classrooms');
    }
};
