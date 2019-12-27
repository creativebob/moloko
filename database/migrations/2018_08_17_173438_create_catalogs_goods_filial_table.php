<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsGoodsFilialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_goods_filial', function (Blueprint $table) {
            $table->bigInteger('catalogs_goods_id')->nullable()->unsigned()->comment('Id каталога товаров');
            $table->foreign('catalogs_goods_id')->references('id')->on('catalogs_goods');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs_goods_filial');
    }
}
