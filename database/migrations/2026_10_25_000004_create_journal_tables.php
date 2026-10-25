<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('journal_entries',function(Blueprint $t){$t->id();$t->foreignId('schedule_lesson_id')->constrained()->cascadeOnDelete();$t->string('topic')->nullable();$t->text('homework')->nullable();$t->string('control_type')->nullable()->index();$t->boolean('is_closed')->default(false)->index();$t->timestamp('closed_at')->nullable();$t->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->unique('schedule_lesson_id');});
  Schema::create('journal_student_records',function(Blueprint $t){$t->id();$t->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();$t->foreignId('student_id')->constrained()->cascadeOnDelete();$t->string('attendance_status')->default('present')->index();$t->string('grade',16)->nullable();$t->text('comment')->nullable();$t->timestamps();$t->unique(['journal_entry_id','student_id']);});
  Schema::create('academic_debts',function(Blueprint $t){$t->id();$t->foreignId('student_id')->constrained()->cascadeOnDelete();$t->foreignId('discipline_id')->constrained()->cascadeOnDelete();$t->foreignId('journal_student_record_id')->nullable()->constrained()->nullOnDelete();$t->string('academic_year')->nullable()->index();$t->string('semester')->nullable();$t->string('reason');$t->string('status')->default('open')->index();$t->date('due_date')->nullable();$t->timestamp('resolved_at')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('academic_debts');Schema::dropIfExists('journal_student_records');Schema::dropIfExists('journal_entries');}
};
