<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_goods', function (Blueprint $table) {
            $table->bigInteger('promotion_id')->unsigned()->comment('Id продвижения');
//            $table->foreign('promotion_id')->references('id')->on('promotions');

            $table->bigInteger('goods_id')->unsigned()->comment('Id товара');
//            $table->foreign('goods_id')->references('id')->on('goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_goods');
    }
}
