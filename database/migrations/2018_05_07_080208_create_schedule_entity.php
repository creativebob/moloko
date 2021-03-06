<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleEntity extends Migration
{

    public function up()
    {
        Schema::create('schedule_entities', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('schedule_id')->nullable()->unsigned()->comment('Id графика работ (расписания)');
            $table->foreign('schedule_id')->references('id')->on('schedules');

            $table->morphs('schedule_entities', 'sched_ent');

            $table->string('mode')->index()->nullable()->comment('Режим');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_entities');
    }
}
