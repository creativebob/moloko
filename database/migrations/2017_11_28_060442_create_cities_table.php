<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city_name', 30)->unique()->index()->comment('Название населенного пункта');
            $table->integer('area_id')->unsigned()->nullable()->comment('Район населенного пункта');
            $table->foreign('area_id')->references('id')->on('areas');
            $table->integer('region_id')->unsigned()->nullable()->comment('Область населенного пункта');
            $table->foreign('region_id')->references('id')->on('regions');
            $table->integer('city_code')->unsigned()->nullable()->comment('Код населенного пункта');
            $table->integer('city_vk_external_id')->unsigned()->nullable()->comment('Внешний Id (из базы vk)');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
