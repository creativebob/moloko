<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntitiesTable extends Migration
{

    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название сущности');

            $table->string('alias')->index()->comment('Название как в базе данных');
            $table->string('model')->index()->comment('Название модели');

            $table->string('view_path')->nullable()->comment('Путь до шаблона отображения');

            $table->boolean('rights')->default(0)->comment('Права на сущность');
            $table->boolean('validation')->default(0)->comment('Дополнительная валидация страницы');
            $table->boolean('feedback')->default(0)->comment('Добавление отзыва');

            $table->bigInteger('page_id')->unsigned()->nullable()->comment('Id страницы');
            $table->foreign('page_id')->references('id')->on('pages');

            $table->bigInteger('ancestor_id')->unsigned()->nullable()->comment('Id предка сущности');
            $table->foreign('ancestor_id')->references('id')->on('entities');

            $table->boolean('statistic')->default(0)->comment('Сбор статистики по сущности для компании');

            $table->boolean('dependence')->default(0)->comment('Филиалозависимость');
            $table->boolean('site')->default(0)->comment('Раздел сайта');

            // Блок тмц
            // $table->boolean('tmc')->default(0)->comment('ТМЦ');
            // $table->bigInteger('consist_id')->unsigned()->nullable()->comment('Id сущности, из которой состоит сущность');
            // $table->foreign('consist_id')->references('id')->on('entities');

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
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entities');
    }

}
