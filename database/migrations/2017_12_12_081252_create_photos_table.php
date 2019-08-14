<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название фото');
            $table->string('title')->nullable()->comment('Заголовок фото');
            $table->text('description')->nullable()->comment('Описание фото');

            $table->string('path')->nullable()->comment('Путь к фото');
            $table->string('link')->nullable()->comment('Ссылка на внешний адрес');

            $table->string('color')->nullable()->comment('Цвет фона ссылки');

            $table->integer('width')->nullable()->comment('Ширина фото');
            $table->integer('height')->nullable()->comment('Высота фото');
            $table->decimal('size', 10, 2)->nullable()->comment('Размер фото');
            $table->string('extension')->nullable()->comment('Расширение фото');

            $table->integer('photo_access')->nullable()->unsigned()->comment('0 - личный, 1 - публичный');

            $table->bigInteger('album_id')->nullable()->unsigned()->comment('Id альбома, физически содержащего в себе фотографию');
            $table->foreign('album_id')->references('id')->on('albums');


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
        Schema::dropIfExists('photos');
    }
}
