<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_categories', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->index()->comment('Название категории товаров');

            $table->text('description')->nullable()->index()->comment('Описание категории товаров');
            $table->text('seo_description')->nullable()->index()->comment('Описание для сайта для категории товаров');

            $table->integer('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->integer('parent_id')->nullable()->unsigned()->comment('Id категории товара');
            $table->foreign('parent_id')->references('id')->on('services_categories');

            // $table->enum('type', ['services', 'goods', 'raws'])->comment('Тип товара (Услуга/товар/сырье)');

            $table->enum('status', ['one', 'set'])->comment('Статус набора (Один/набор)');

            $table->integer('services_mode_id')->nullable()->unsigned()->comment('Вид продукции');
            $table->foreign('services_mode_id')->references('id')->on('services_modes');

            $table->integer('category_status')->unsigned()->nullable()->comment('Статус категории');

            $table->integer('category_id')->unsigned()->nullable()->comment('Id категории, пишется каждому вложенному пункту');
            $table->foreign('category_id')->references('id')->on('services_categories');

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
        Schema::dropIfExists('services_categories');
    }
}
