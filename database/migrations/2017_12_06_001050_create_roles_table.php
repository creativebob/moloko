<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role_name')->index()->unique()->comment('Имя категории пользователей');
            $table->string('role_description')->index()->nullable()->comment('Описание категории');

            $table->integer('category_right_id')->nullable()->unsigned()->comment('Категория пользователей');
            $table->foreign('category_right_id')->references('id')->on('category_rights');

            $table->integer('department_id')->nullable()->unsigned()->comment('ID филиала');
            $table->foreign('department_id')->references('id')->on('departments');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
