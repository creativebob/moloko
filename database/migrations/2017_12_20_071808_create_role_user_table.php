<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('role_id')->nullable()->unsigned()->comment('ID правила доступа');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->integer('department_id')->nullable()->unsigned()->comment('ID отдела / филиала');
            $table->foreign('department_id')->references('id')->on('departments');

            $table->integer('position_id')->nullable()->unsigned()->comment('ID должности');
            $table->foreign('position_id')->references('id')->on('positions');

            $table->integer('user_id')->nullable()->unsigned()->comment('ID пользователя');
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
        Schema::dropIfExists('role_user');
    }
}
