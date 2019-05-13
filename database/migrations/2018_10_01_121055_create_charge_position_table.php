<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargePositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_position', function (Blueprint $table) {
            $table->bigInteger('charge_id')->unsigned()->comment('ID обязанности');
            $table->foreign('charge_id')->references('id')->on('charges');

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
        Schema::dropIfExists('charge_position');
    }
}
