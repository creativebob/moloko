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
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название альбома');

            $table->bigInteger('category_id')->nullable()->unsigned()->comment('Id категории альбомов');
            $table->foreign('category_id')->references('id')->on('albums_categories');

            $table->boolean('personal')->default(0)->comment('Личный');

            $table->string('alias')->nullable()->index()->comment('Алиас альбома');
            $table->string('slug')->nullable()->index()->comment('Слаг альбома');

            $table->integer('delay')->nullable()->unsigned()->comment('Задержка во времени, сек');

            $table->string('photo_id')->index()->nullable()->comment('Обложка альбома');

            $table->text('description')->nullable()->comment('Описание альбома');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(0)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

            $table->timestamps();
            // $table->softDeletes();
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
