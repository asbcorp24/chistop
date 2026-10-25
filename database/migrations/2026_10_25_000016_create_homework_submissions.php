<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('schedule_lessons', fn (Blueprint $t) => $t->dateTime('homework_due_at')->nullable());
  Schema::create('homework_submissions', function (Blueprint $t) {
   $t->id(); $t->foreignId('schedule_lesson_id')->constrained()->cascadeOnDelete();
   $t->foreignId('student_id')->constrained()->cascadeOnDelete();
   $t->text('answer_text')->nullable(); $t->string('file_path')->nullable(); $t->string('original_name')->nullable();
   $t->string('status')->default('submitted'); $t->text('teacher_comment')->nullable();
   $t->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
   $t->timestamp('submitted_at')->nullable(); $t->timestamp('checked_at')->nullable(); $t->timestamps();
   $t->unique(['schedule_lesson_id','student_id']);
  });
 }
 public function down(): void {
  Schema::dropIfExists('homework_submissions');
  Schema::table('schedule_lessons', fn (Blueprint $t) => $t->dropColumn('homework_due_at'));
 }
};