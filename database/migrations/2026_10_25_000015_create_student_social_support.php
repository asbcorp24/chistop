<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('student_social_supports', function(Blueprint $table) {
   $table->id(); $table->foreignId('student_id')->constrained()->cascadeOnDelete();
   $table->string('support_type',120); $table->string('basis')->nullable();
   $table->date('starts_on')->nullable(); $table->date('ends_on')->nullable();
   $table->string('status',30)->default('active'); $table->text('note')->nullable();
   $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
  });
  Schema::create('student_social_followups', function(Blueprint $table) {
   $table->id(); $table->foreignId('student_id')->constrained()->cascadeOnDelete();
   $table->date('started_on'); $table->date('ended_on')->nullable(); $table->string('reason',500);
   $table->string('responsible')->nullable(); $table->text('actions')->nullable(); $table->text('result')->nullable();
   $table->string('status',30)->default('active'); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('student_social_followups'); Schema::dropIfExists('student_social_supports'); }
};
