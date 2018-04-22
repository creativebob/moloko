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
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->index()->comment('Название фото');
            $table->string('description')->nullable()->comment('Описание фото');

            $table->string('path')->nullable()->comment('Путь к фото');
            $table->string('alias')->nullable()->comment('Алиас фото');

            $table->string('width')->nullable()->comment('Ширина фото');
            $table->string('height')->nullable()->comment('Высота фото');
            $table->string('size')->nullable()->comment('Размер фото');
            $table->string('extension')->nullable()->comment('Расширение фото');

            $table->integer('album_id')->nullable()->unsigned()->comment('Id категории в которой находиться альбом');
            $table->foreign('album_id')->references('id')->on('albums');

            $table->integer('photo_access')->nullable()->unsigned()->comment('0 - личный, 1 - публичный');
            

            $table->integer('sort')->nullable()->unsigned()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');

            $table->timestamps();
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
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
        Schema::dropIfExists('photos');
    }
}
