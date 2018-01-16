<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('menus', function (Blueprint $table) {
      $table->increments('id');
      $table->string('menu_name')->nullable()->comment('Имя категории меню');
      $table->string('menu_icon')->nullable()->comment('Имя иконки меню');

      $table->integer('menu_parent_id')->unsigned()->nullable()->comment('Id родителя пункта меню');
      $table->foreign('menu_parent_id')->references('id')->on('menus');

      $table->integer('navigation_id')->unsigned()->nullable()->comment('Id названия меню');
      $table->foreign('navigation_id')->references('id')->on('navigations');

      $table->integer('page_id')->unsigned()->nullable()->comment('Id страницы пункта меню');
      $table->foreign('page_id')->references('id')->on('pages');

      $table->integer('table_id')->unsigned()->nullable()->comment('Id таблицы пункта меню');

      $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
      $table->foreign('company_id')->references('id')->on('companies');

      $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
      $table->foreign('author_id')->references('id')->on('users');

      $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
      $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
      $table->timestamps();
      $table->dateTime('moderated_at')->nullable()->comment('Дата модерации');
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
