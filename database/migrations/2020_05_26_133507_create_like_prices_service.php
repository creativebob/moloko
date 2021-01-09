<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikePricesService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('like_prices_service', function (Blueprint $table) {
            $table->bigInteger('prices_service_id')->nullable()->unsigned()->comment('Id прайса услуги');
//            $table->foreign('prices_service_id')->references('id')->on('prices_services');

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
        Schema::dropIfExists('like_prices_service');
    }
}
