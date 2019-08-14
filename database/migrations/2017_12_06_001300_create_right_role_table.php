<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRightRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('right_role', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('right_id')->nullable()->unsigned()->comment('ID правила');
            $table->foreign('right_id')->references('id')->on('rights');

            $table->bigInteger('role_id')->nullable()->unsigned()->comment('ID категории пользователя');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->boolean('display')->default(0)->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->timestamps();
            $table->boolean('moderation')->default(0)->comment('Модерация');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('right_role');
    }
}
