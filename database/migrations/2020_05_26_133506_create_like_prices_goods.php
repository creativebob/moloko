<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikePricesGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('like_prices_goods', function (Blueprint $table) {
            $table->bigInteger('prices_goods_id')->nullable()->unsigned()->comment('Id прайса товара');
//            $table->foreign('prices_goods_id')->references('id')->on('prices_goods');

            $table->bigInteger('user_id')->nullable()->unsigned()->comment('Id пользователя');
//            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('like_prices_goods');
    }
}
