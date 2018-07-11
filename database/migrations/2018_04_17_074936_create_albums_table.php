<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->index()->comment('Название альбома');

            $table->integer('albums_category_id')->nullable()->unsigned()->comment('Id категории в которой находиться альбом');
            $table->foreign('albums_category_id')->references('id')->on('albums_categories');

            $table->integer('access')->nullable()->unsigned()->comment('0 - личный, 1 - публичный');
            $table->string('alias')->index()->comment('Алиас альбома');
            $table->integer('delay')->nullable()->unsigned()->comment('Задержка во времени, сек');

            $table->string('photo_id')->index()->nullable()->comment('Обложка альбома');
            
            $table->string('description')->index()->nullable()->comment('Описание альбома');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');

            $table->timestamps();
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albums');
    }
}
