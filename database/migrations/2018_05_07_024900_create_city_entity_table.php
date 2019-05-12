<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_entity', function (Blueprint $table) {

            $table->bigInteger('city_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('city_id')->references('id')->on('cities');

            $table->morphs('city_entity');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_entity');
    }
}
