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

            $table->string('name')->nullable()->comment('Имя категории меню');
            $table->string('icon')->nullable()->comment('Имя иконки меню');
            $table->string('alias')->nullable()->comment('Ссылка на страницу');
            $table->string('tag')->nullable()->comment('Ключ для поиска');

            $table->bigInteger('page_id')->unsigned()->nullable()->comment('Id страницы пункта меню');
            $table->foreign('page_id')->references('id')->on('pages');

            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Id родителя');
            $table->foreign('parent_id')->references('id')->on('menus');

            $table->bigInteger('category_id')->unsigned()->nullable()->comment('Id категории');
            $table->foreign('category_id')->references('id')->on('menus');

            $table->boolean('new_blank')->default(0)->comment('Новая вкладка');


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
