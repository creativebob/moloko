<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_user', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('notification_id')->nullable()->unsigned()->comment('ID оповещения');
            $table->foreign('notification_id')->references('id')->on('notifications');

            $table->bigInteger('user_id')->nullable()->unsigned()->comment('ID пользователя');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_user');
    }
}
