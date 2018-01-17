<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position_role', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('position_id')->nullable()->unsigned()->comment('ID должности');
            $table->foreign('position_id')->references('id')->on('positions');

            $table->integer('role_id')->nullable()->unsigned()->comment('ID категории пользователя');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->timestamps();
            $table->integer('moderated')->nullable()->unsigned()->comment('Статус модерации');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('position_role');
    }
}
