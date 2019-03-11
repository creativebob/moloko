<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsItemsGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_items_goods', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('catalogs_item_id')->nullable()->unsigned()->comment('ID пункта каталога');
            $table->foreign('catalogs_item_id')->references('id')->on('catalogs_items');

            $table->integer('goods_id')->nullable()->unsigned()->comment('ID товара');
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
        //
    }
}
