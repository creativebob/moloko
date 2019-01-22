<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration
{

    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->index()->comment('Название сущности');

            $table->string('alias')->index()->comment('Название как в базе данных');
            $table->string('model')->index()->comment('Название модели');

            $table->integer('rights_minus')->unsigned()->nullable()->comment('Исключает настройку прав на сущность при равной 1');
            $table->integer('validation_minus')->unsigned()->nullable()->comment('Исключает настройку дополнительной валидации страницы');
            $table->integer('feedback_minus')->unsigned()->nullable()->comment('Исключает добавление отзыва к сущности из под интерфейса');

            $table->boolean('statistic')->default(0)->comment('Сбор статистики по сущности дял компании');

            $table->boolean('dependence')->default(0)->comment('Филиалозависимость');
            $table->boolean('site')->default(0)->comment('Раздел сайта');


            // Общие настройки
            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entities');
    }

}
