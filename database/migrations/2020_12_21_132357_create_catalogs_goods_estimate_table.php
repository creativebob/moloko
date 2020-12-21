<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogsGoodsEstimateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_goods_estimate', function (Blueprint $table) {
            $table->bigInteger('catalogs_goods_id')->nullable()->unsigned()->comment('Id каталога товаров');
//            $table->foreign('catalogs_goods_id')->references('id')->on('catalogs_goods');

            $table->bigInteger('estimate_id')->nullable()->unsigned()->comment('Id сметы');
//            $table->foreign('estimate_id')->references('id')->on('estimates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs_goods_estimate');
    }
}
