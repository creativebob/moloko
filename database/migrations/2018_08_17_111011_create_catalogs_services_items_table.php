<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsServicesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_services_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('catalogs_service_id')->nullable()->unsigned()->comment('Id каталога');
            $table->foreign('catalogs_service_id')->references('id')->on('catalogs_services');

            $table->string('name')->index()->comment('Название');
            $table->string('slug')->index()->nullable()->comment('Слаг');
            $table->integer('level')->nullable()->unsigned()->comment('Уровень вложенности');

            $table->string('header')->nullable()->comment('Заголовок');
            $table->string('title')->nullable()->comment('Название страницы');
            $table->text('description')->nullable()->comment('Описание');
            $table->text('seo_description')->nullable()->comment('Описание для сайта');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->string('color', 7)->nullable()->comment('Цвет');

            $table->boolean('is_discount')->default(1)->unsigned()->comment('Режим скидок');

            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Id родителя');
            $table->foreign('parent_id')->references('id')->on('catalogs_services_items');

            $table->bigInteger('category_id')->unsigned()->nullable()->comment('Id категории, пишется каждому вложенному пункту');
            $table->foreign('category_id')->references('id')->on('catalogs_services_items');

            $table->bigInteger('display_mode_id')->unsigned()->default(1)->comment('Id типа отображения');
            $table->foreign('display_mode_id')->references('id')->on('display_modes');

            $table->boolean('is_controllable_mode')->default(0)->comment('Контроль режима');
            $table->boolean('is_show_subcategory')->default(0)->comment('Отображать ВСЕ для субкатегорий');
            $table->boolean('is_hide_submenu')->default(0)->comment('Не отображать субменю');

            $table->bigInteger('directive_category_id')->nullable()->unsigned()->comment('Основная мера');
            $table->foreign('directive_category_id')->references('id')->on('units_categories');


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
        Schema::dropIfExists('catalogs_services_items');
    }
}
