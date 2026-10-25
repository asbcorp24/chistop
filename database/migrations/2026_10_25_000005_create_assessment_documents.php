<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('assessment_sheets',function(Blueprint $t){$t->id();$t->foreignId('study_group_id')->constrained()->cascadeOnDelete();$t->foreignId('discipline_id')->constrained()->cascadeOnDelete();$t->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();$t->string('academic_year');$t->string('semester')->nullable();$t->string('control_type');$t->date('assessment_date')->nullable();$t->string('number')->nullable()->unique();$t->string('status')->default('draft')->index();$t->timestamp('closed_at')->nullable();$t->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();});
  Schema::create('assessment_sheet_rows',function(Blueprint $t){$t->id();$t->foreignId('assessment_sheet_id')->constrained()->cascadeOnDelete();$t->foreignId('student_id')->constrained()->cascadeOnDelete();$t->string('result',32)->nullable();$t->string('grade',16)->nullable();$t->text('comment')->nullable();$t->timestamps();$t->unique(['assessment_sheet_id','student_id']);});
  Schema::create('retakes',function(Blueprint $t){$t->id();$t->foreignId('academic_debt_id')->constrained()->cascadeOnDelete();$t->foreignId('student_id')->constrained()->cascadeOnDelete();$t->foreignId('discipline_id')->constrained()->cascadeOnDelete();$t->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();$t->string('direction_number')->nullable()->unique();$t->dateTime('scheduled_at')->nullable();$t->string('room')->nullable();$t->string('status')->default('scheduled')->index();$t->string('result',32)->nullable();$t->string('grade',16)->nullable();$t->text('comment')->nullable();$t->timestamp('completed_at')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('retakes');Schema::dropIfExists('assessment_sheet_rows');Schema::dropIfExists('assessment_sheets');}
};