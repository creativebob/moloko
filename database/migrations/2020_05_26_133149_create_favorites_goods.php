<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesGoods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorites_goods', function (Blueprint $table) {
            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id товара');
//            $table->foreign('goods_id')->references('id')->on('goods');

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
        Schema::dropIfExists('favorites_goods');
    }
}
