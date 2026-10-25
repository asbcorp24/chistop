<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('disciplines', function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->cascadeOnDelete();$t->string('external_id')->nullable();$t->string('code')->nullable();$t->string('name');$t->boolean('is_active')->default(true);$t->timestamps();$t->unique(['organization_id','external_id']);});
  Schema::create('schedule_lessons', function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->cascadeOnDelete();$t->string('external_id');$t->foreignId('study_group_id')->constrained()->cascadeOnDelete();$t->foreignId('discipline_id')->constrained()->cascadeOnDelete();$t->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();$t->date('lesson_date')->index();$t->time('starts_at');$t->time('ends_at');$t->string('room')->nullable();$t->string('subgroup')->nullable();$t->string('lesson_type')->nullable();$t->string('status')->default('scheduled')->index();$t->json('source_data')->nullable();$t->timestamps();$t->unique(['organization_id','external_id']);});
  Schema::create('teacher_workloads', function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->cascadeOnDelete();$t->string('external_id');$t->foreignId('teacher_id')->constrained()->cascadeOnDelete();$t->foreignId('study_group_id')->nullable()->constrained()->nullOnDelete();$t->foreignId('discipline_id')->constrained()->cascadeOnDelete();$t->string('academic_year')->index();$t->string('semester')->nullable();$t->decimal('hours',8,2)->default(0);$t->string('workload_type')->nullable();$t->json('source_data')->nullable();$t->timestamps();$t->unique(['organization_id','external_id']);});
 }
 public function down(): void {Schema::dropIfExists('teacher_workloads');Schema::dropIfExists('schedule_lessons');Schema::dropIfExists('disciplines');}
};
