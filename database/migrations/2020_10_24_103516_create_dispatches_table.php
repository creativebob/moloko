<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('mailing_id')->unsigned()->nullable()->comment('Id рассылки');
            $table->foreign('mailing_id')->references('id')->on('mailings');

            $table->bigInteger('subscriber_id')->unsigned()->nullable()->comment('Id подписчика');
            $table->foreign('subscriber_id')->references('id')->on('subscribers');

            $table->string('email')->comment('Email');

            $table->timestamp('sended_at')->nullable()->comment('Время отправки');

            $table->timestamp('delivered_at')->nullable()->comment('Время доставки');
            $table->timestamp('opened_at')->nullable()->comment('Время открытия');
            $table->timestamp('spamed_at')->nullable()->comment('Время добавления в спам');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

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
        Schema::dropIfExists('dispatches');
    }
}
