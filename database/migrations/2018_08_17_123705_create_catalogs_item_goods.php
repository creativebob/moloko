<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsItemGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_item_goods', function (Blueprint $table) {
            $table->bigInteger('catalogs_item_id')->nullable()->unsigned()->comment('Id пункта каталога');
            $table->foreign('catalogs_item_id')->references('id')->on('catalogs_items');

            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id товара');
            $table->foreign('goods_id')->references('id')->on('goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs_item_goods');
    }
}
