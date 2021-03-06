<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountPriceGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_price_goods', function (Blueprint $table) {
            $table->bigInteger('price_goods_id')->unsigned()->comment('Id прайса товара');
//            $table->foreign('price_goods_id')->references('id')->on('prices_goods');

            $table->bigInteger('discount_id')->unsigned()->comment('Id скидки');
//            $table->foreign('discount_id')->references('id')->on('discounts');
    
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_price_goods');
    }
}
