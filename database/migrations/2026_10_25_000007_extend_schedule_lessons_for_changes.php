<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void {
  Schema::table('schedule_lessons', function (Blueprint $t) {
   $t->unsignedSmallInteger('pair_number')->nullable()->after('lesson_date')->index();
   $t->string('change_type')->nullable()->after('status')->index();
   $t->string('change_note')->nullable()->after('change_type');
   $t->string('original_external_id')->nullable()->after('external_id')->index();
  });
 }
 public function down(): void {
  Schema::table('schedule_lessons', fn (Blueprint $t) => $t->dropColumn(['pair_number','change_type','change_note','original_external_id']));
 }
};
