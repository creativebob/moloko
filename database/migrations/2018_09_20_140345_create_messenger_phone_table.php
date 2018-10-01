<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessengerPhoneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messenger_phone', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('messenger_id')->unsigned()->comment('ID мессенджера');
            // $table->foreign('messenger_id')->references('id')->on('messengers');

            $table->integer('phone_id')->unsigned()->comment('ID телефона');
            // $table->foreign('phone_id')->references('id')->on('phones');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messenger_phone');
    }
}
