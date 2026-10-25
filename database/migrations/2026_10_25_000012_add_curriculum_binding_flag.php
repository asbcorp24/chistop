<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{Schema::table('schedule_lessons',function(Blueprint $t){$t->boolean('curriculum_auto_bound')->default(false)->after('curriculum_topic_id')->index();});}public function down():void{Schema::table('schedule_lessons',fn(Blueprint $t)=>$t->dropColumn('curriculum_auto_bound'));}};
