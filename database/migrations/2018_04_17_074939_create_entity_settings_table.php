<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_settings', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('entity_id')->nullable()->unsigned()->comment('Id сущности');
            // $table->foreign('entity_id')->references('id')->on('entities');
             $table->string('entity')->nullable()->comment('Имя сущности');

            $table->string('name')->index()->comment('Название настройки');
            $table->string('description')->nullable()->comment('Описание настройки');

            $table->integer('img_small_width')->nullable()->unsigned()->comment('Ширина маленького изображения');
            $table->integer('img_small_height')->nullable()->unsigned()->comment('Высота маленького изображения');
            $table->integer('img_medium_width')->nullable()->unsigned()->comment('Ширина среднего изображения');
            $table->integer('img_medium_height')->nullable()->unsigned()->comment('Высота среднего изображения');
            $table->integer('img_large_width')->nullable()->unsigned()->comment('Ширина большого изображения');
            $table->integer('img_large_height')->nullable()->unsigned()->comment('Высота большого изображения');

            $table->string('img_formats')->nullable()->comment('Допустимые форматы');
            $table->integer('upload_mode')->nullable()->unsigned()->comment('Режим загрузки изображений (1 - строгий / null - простой)');

            $table->integer('img_min_width')->nullable()->unsigned()->comment('Минимальная ширина изображения');
            $table->integer('img_min_height')->nullable()->unsigned()->comment('Минимальная высота изображения');
            $table->integer('img_max_size')->nullable()->unsigned()->comment('Размер изображения');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

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
        Schema::dropIfExists('entity_settings');
    }
}
