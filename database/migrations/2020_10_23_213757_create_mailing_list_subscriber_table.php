<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingListSubscriberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_list_subscriber', function (Blueprint $table) {
            $table->bigInteger('mailing_list_id')->unsigned()->nullable()->comment('Id списка рассылки');
//            $table->foreign('mailing_list_id')->references('id')->on('mailing_list');

            $table->bigInteger('subscriber_id')->unsigned()->nullable()->comment('Id подписчика');
//            $table->foreign('subscriber_id')->references('id')->on('subscribers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailing_list_subscriber');
    }
}
