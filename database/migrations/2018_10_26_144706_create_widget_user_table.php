<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widget_user', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('widget_id')->unsigned()->comment('ID виджета');
            // $table->foreign('charge_id')->references('id')->on('charges');

            $table->integer('user_id')->unsigned()->comment('ID пользователя');
            // $table->foreign('user_id')->references('id')->on('users');

            $table->integer('collapse')->nullable()->unsigned()->comment('Свернут');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('widget_user');
    }
}
