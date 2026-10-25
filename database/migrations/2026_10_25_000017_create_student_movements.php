<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('student_movements', function (Blueprint $t) {
   $t->id(); $t->foreignId('student_id')->constrained()->cascadeOnDelete();
   $t->foreignId('from_group_id')->nullable()->constrained('study_groups')->nullOnDelete();
   $t->foreignId('to_group_id')->nullable()->constrained('study_groups')->nullOnDelete();
   $t->string('type',40)->index(); $t->date('effective_on')->index();
   $t->string('order_number')->nullable(); $t->date('order_date')->nullable(); $t->text('basis')->nullable();
   $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('student_movements'); }
};