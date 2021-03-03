<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{

    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('navigation_id')->unsigned()->nullable()->comment('Id навигации');
            $table->foreign('navigation_id')->references('id')->on('navigations');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->string('name')->nullable()->comment('Название');
            $table->string('slug')->nullable()->index()->comment('Слаг');
            $table->integer('level')->nullable()->unsigned()->comment('Уровень вложенности');

            $table->string('icon')->nullable()->comment('Имя иконки меню');
            $table->string('alias')->nullable()->comment('Ссылка на страницу');
            $table->string('tag')->nullable()->comment('Ключ для поиска');

            $table->string('title')->nullable()->comment('Тег title для ссылки');

            $table->text('description')->nullable()->comment('Описание');
            $table->text('seo_description')->nullable()->comment('Описание для сайта');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('page_id')->unsigned()->nullable()->comment('Id страницы');
            $table->foreign('page_id')->references('id')->on('pages');

            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Id родителя');
            $table->foreign('parent_id')->references('id')->on('menus');

            $table->bigInteger('category_id')->unsigned()->nullable()->comment('Id категории');
            $table->foreign('category_id')->references('id')->on('menus');

            $table->boolean('new_blank')->default(0)->comment('Новая вкладка');
            $table->boolean('text_hidden')->default(0)->comment('Скрыть текст ссылки');
            $table->boolean('is_nofollow')->default(0)->comment('Запрет индексации');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(1)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

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
    Schema::dropIfExists('menus');
}
}
