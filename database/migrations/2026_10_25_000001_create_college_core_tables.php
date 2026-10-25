<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('inn', 12)->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('name');
            $table->string('qualification')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['organization_id', 'code']);
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_id')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['organization_id', 'external_id']);
        });

        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialty_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('curator_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->unsignedSmallInteger('admission_year')->nullable();
            $table->date('start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->unique(['organization_id', 'external_id']);
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('personal_file_number')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->unique(['organization_id', 'external_id']);
            $table->unique(['organization_id', 'personal_file_number']);
        });

        Schema::create('student_group_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_group_id')->constrained()->cascadeOnDelete();
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->string('reason')->nullable();
            $table->string('order_number')->nullable();
            $table->date('order_date')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'started_at', 'ended_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_group_enrollments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('study_groups');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('specialties');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('organizations');
    }
};
