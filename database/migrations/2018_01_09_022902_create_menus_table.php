<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{

    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('navigation_id')->unsigned()->nullable()->comment('Id навигации');
            $table->foreign('navigation_id')->references('id')->on('navigations');

            $table->string('name')->nullable()->comment('Имя категории меню');
            $table->string('icon')->nullable()->comment('Имя иконки меню');
            $table->string('alias')->nullable()->comment('Ссылка на страницу');
            $table->string('tag')->nullable()->comment('Ключ для поиска');

            $table->integer('page_id')->unsigned()->nullable()->comment('Id страницы пункта меню');
            $table->foreign('page_id')->references('id')->on('pages');

            $table->integer('parent_id')->nullable()->unsigned()->comment('Id родителя');
            $table->foreign('parent_id')->references('id')->on('menus');

            $table->integer('category_id')->unsigned()->nullable()->comment('Id категории');
            $table->foreign('category_id')->references('id')->on('menus');

            $table->boolean('new_blank')->default(0)->comment('Новая вкладка');


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
