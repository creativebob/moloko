<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooklistUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booklist_user', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('booklist_id')->nullable()->unsigned()->comment('ID списка');
            $table->foreign('booklist_id')->references('id')->on('booklists');

            $table->bigInteger('user_id')->nullable()->unsigned()->comment('ID пользователя');
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('booklist_user');
    }
}
