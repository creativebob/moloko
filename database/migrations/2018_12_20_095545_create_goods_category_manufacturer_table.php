<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsCategoryManufacturerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_category_manufacturer', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('goods_category_id')->unsigned()->comment('Id категории товаров');
            $table->foreign('goods_category_id')->references('id')->on('goods_categories');

            $table->integer('manufacturer_id')->unsigned()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('companies');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_category_manufacturer');
    }
}
