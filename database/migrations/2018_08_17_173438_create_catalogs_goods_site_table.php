<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsGoodsSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_goods_site', function (Blueprint $table) {

            $table->bigInteger('catalogs_goods_id')->nullable()->unsigned()->comment('Id каталога товаров');
            $table->foreign('catalogs_goods_id')->references('id')->on('catalogs_goods');

            $table->bigInteger('site_id')->nullable()->unsigned()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs_goods_site');
    }
}
