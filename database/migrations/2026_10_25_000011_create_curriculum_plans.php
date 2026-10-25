<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('curriculum_plans',function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->cascadeOnDelete();$t->foreignId('study_group_id')->constrained()->restrictOnDelete();$t->foreignId('discipline_id')->constrained()->restrictOnDelete();$t->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();$t->string('academic_year')->nullable()->index();$t->string('semester')->nullable();$t->string('title')->nullable();$t->unsignedInteger('version')->default(1);$t->text('purpose')->nullable();$t->text('learning_outcomes')->nullable();$t->text('requirements')->nullable();$t->text('assessment')->nullable();$t->text('literature')->nullable();$t->text('resources')->nullable();$t->string('status')->default('active')->index();$t->string('approval_status')->default('draft')->index();$t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamp('approved_at')->nullable();$t->timestamps();$t->unique(['organization_id','study_group_id','discipline_id','teacher_id','academic_year','semester'],'curriculum_plan_unique');});
  Schema::create('curriculum_topics',function(Blueprint $t){$t->id();$t->foreignId('curriculum_plan_id')->constrained()->cascadeOnDelete();$t->unsignedInteger('sort_order')->default(0);$t->string('section')->nullable();$t->text('title');$t->decimal('planned_hours',5,2)->default(1);$t->date('planned_on')->nullable();$t->string('control_type')->nullable();$t->boolean('is_checkpoint')->default(false);$t->text('note')->nullable();$t->timestamps();});
  Schema::table('schedule_lessons',function(Blueprint $t){$t->foreignId('curriculum_topic_id')->nullable()->after('discipline_id')->constrained('curriculum_topics')->nullOnDelete();});
 }
 public function down(): void {Schema::table('schedule_lessons',fn(Blueprint $t)=>$t->dropConstrainedForeignId('curriculum_topic_id'));Schema::dropIfExists('curriculum_topics');Schema::dropIfExists('curriculum_plans');}
};
