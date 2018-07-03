<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{

    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('place_id')->nullable()->unsigned()->comment('Id помещения');
            $table->foreign('place_id')->references('id')->on('places');
            
            $table->integer('places_type_id')->nullable()->unsigned()->comment('Id сущности связанной с метрикой');
            $table->foreign('places_type_id')->references('id')->on('places_types');

        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
