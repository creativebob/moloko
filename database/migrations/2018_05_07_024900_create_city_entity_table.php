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
            $table->increments('id');

            $table->integer('city_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('city_id')->references('id')->on('cities');
            
            $table->integer('city_entity_id')->unsigned()->comment('Id сущности');
            $table->string('city_entity_type')->comment('Сущность');

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
        Schema::dropIfExists('city_entity');
    }
}
