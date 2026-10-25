<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{
 Schema::create('curator_work_plans',function(Blueprint $t){$t->id();$t->foreignId('study_group_id')->constrained()->cascadeOnDelete();$t->foreignId('teacher_id')->constrained()->cascadeOnDelete();$t->string('academic_year')->nullable()->index();$t->string('title')->default('План воспитательной работы');$t->longText('content')->nullable();$t->string('status',30)->default('draft')->index();$t->timestamps();$t->unique(['study_group_id','teacher_id','academic_year'],'curator_plan_group_teacher_year_uq');});
 Schema::create('student_characteristics',function(Blueprint $t){$t->id();$t->foreignId('student_id')->constrained()->cascadeOnDelete();$t->foreignId('teacher_id')->constrained()->cascadeOnDelete();$t->string('request_basis',500)->nullable();$t->longText('content');$t->date('prepared_on');$t->timestamps();});
}public function down():void{Schema::dropIfExists('student_characteristics');Schema::dropIfExists('curator_work_plans');}};
