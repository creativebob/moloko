<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_event', function (Blueprint $table) {
            $table->bigInteger('process_id')->nullable()->unsigned()->comment('Id процесса');
//            $table->foreign('process_id')->references('id')->on('processes');

            $table->bigInteger('event_id')->nullable()->unsigned()->comment('Id события');
//            $table->foreign('event_id')->references('id')->on('events');

            $table->integer('value')->nullable()->unsigned()->comment('Значение');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_event');
    }
}
