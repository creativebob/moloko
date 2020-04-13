<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_position', function (Blueprint $table) {
            $table->bigInteger('process_id')->nullable()->unsigned()->comment('Id процесса');
//            $table->foreign('process_id')->references('id')->on('processes');

            $table->bigInteger('position_id')->nullable()->unsigned()->comment('Id должности');
//            $table->foreign('position_id')->references('id')->on('positions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_position');
    }
}
