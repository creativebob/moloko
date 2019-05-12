<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widget_position', function (Blueprint $table) {
            $table->bigInteger('widget_id')->unsigned()->comment('ID виджета');
            $table->foreign('widget_id')->references('id')->on('widgets');

            $table->bigInteger('position_id')->unsigned()->comment('ID должности');
            $table->foreign('position_id')->references('id')->on('positions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('widget_position');
    }
}
