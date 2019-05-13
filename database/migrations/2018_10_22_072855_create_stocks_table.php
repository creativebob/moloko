.<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{

    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->bigInteger('place_id')->nullable()->unsigned()->comment('Id помещения');
            $table->foreign('place_id')->references('id')->on('places');

            $table->bigInteger('stored_id')->nullable()->unsigned()->comment('Id сущности которая хранится на складе');
            $table->string('stored_type')->nullable()->comment('Сущность');

            $table->integer('count')->nullable()->unsigned()->comment('Кол-во');

        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
