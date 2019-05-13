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
            $table->bigIncrements('id');

            $table->bigInteger('widget_id')->unsigned()->comment('ID виджета');
            $table->foreign('widget_id')->references('id')->on('widgets');

            $table->bigInteger('user_id')->unsigned()->comment('ID пользователя');
            $table->foreign('user_id')->references('id')->on('users');

            $table->boolean('collapse')->default(0)->comment('Свернут');
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
