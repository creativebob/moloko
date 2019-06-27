<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название категории оборудования');

            $table->text('description')->nullable()->comment('Описание категории оборудования');
            $table->text('seo_description')->nullable()->comment('Описание для сайта для категории оборудования');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Id категории товара');
            $table->foreign('parent_id')->references('id')->on('equipments_categories');

            $table->bigInteger('category_id')->unsigned()->nullable()->comment('Id категории, пишется каждому вложенному пункту');
            $table->foreign('category_id')->references('id')->on('equipments_categories');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

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
        Schema::dropIfExists('equipments_categories');
    }
}
