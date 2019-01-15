<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_site', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('menu_id')->nullable()->unsigned()->comment('Id раздела меню');
            $table->foreign('menu_id')->references('id')->on('menus');

            $table->integer('site_id')->nullable()->unsigned()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');


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
        Schema::dropIfExists('menu_site');
    }
}
