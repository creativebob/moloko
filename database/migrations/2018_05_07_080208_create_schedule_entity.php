<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleEntity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_entity', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('schedule_id')->nullable()->unsigned()->comment('Id графика работ (расписания)');
            $table->foreign('schedule_id')->references('id')->on('schedules');
            
            $table->integer('entity_id')->nullable()->unsigned()->comment('Id сущности связанной с расписанием');
            $table->string('entity')->index()->comment('Сущность обьекта');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_entity');
    }
}
