<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_logins', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->nullable()->unsigned()->comment('Id пользователя');
//            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamp('logined_at')->nullable()->comment('Время авторизации');

            $table->ipAddress('ip')->nullable()->comment('Ip адрес');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_logins');
    }
}
