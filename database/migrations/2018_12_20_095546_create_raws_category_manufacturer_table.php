<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawsCategoryManufacturerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raws_category_manufacturer', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('raws_category_id')->unsigned()->comment('Id категории товаров');
            // $table->foreign('raws_category_id')->references('id')->on('raws_categories');

            $table->integer('manufacturer_id')->unsigned()->comment('Id производителя');
            // $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raws_category_manufacturer');
    }
}
