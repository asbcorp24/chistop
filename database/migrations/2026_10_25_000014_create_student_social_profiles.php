<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('student_social_profiles', function(Blueprint $table) {
   $table->id();
   $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
   $table->string('family_status')->nullable();
   $table->unsignedSmallInteger('family_members')->nullable();
   $table->unsignedSmallInteger('children_in_family')->nullable();
   $table->string('housing_status')->nullable();
   $table->boolean('lives_in_dormitory')->default(false);
   $table->boolean('low_income_family')->default(false);
   $table->boolean('large_family')->default(false);
   $table->boolean('orphan_status')->default(false);
   $table->boolean('guardianship')->default(false);
   $table->boolean('needs_accessibility')->default(false);
   $table->boolean('needs_followup')->default(false);
   $table->string('contact_person')->nullable();
   $table->string('contact_phone')->nullable();
   $table->text('notes')->nullable();
   $table->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('student_social_profiles'); }
};
