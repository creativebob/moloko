<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionPriceGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_price_goods', function (Blueprint $table) {
            $table->bigInteger('promotion_id')->nullable()->unsigned()->comment('Id акции');
            $table->foreign('promotion_id')->references('id')->on('promotions');

            $table->bigInteger('price_goods_id')->nullable()->unsigned()->comment('Id прайса товаров');
            $table->foreign('price_goods_id')->references('id')->on('prices_goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_price_goods');
    }
}
