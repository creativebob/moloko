<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityFilialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_filial', function (Blueprint $table) {
            $table->bigInteger('city_id')->nullable()->unsigned()->comment('Id города');
//            $table->foreign('city_id')->references('id')->on('cities');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
//            $table->foreign('filial_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_filial');
    }
}
