<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название товара');

            $table->integer('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->enum('set_status', ['one', 'set'])->comment('Статус набора (Один/набор)');

            $table->text('description')->nullable()->comment('Описание товара');

            $table->integer('unit_id')->nullable()->unsigned()->comment('ID еденицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->integer('rule_id')->nullable()->unsigned()->comment('ID правила определения цены');
            // $table->foreign('rule_id')->references('id')->on('rules');

            $table->integer('goods_category_id')->nullable()->unsigned()->comment('Id категории в которой находиться товар');
            $table->foreign('goods_category_id')->references('id')->on('goods_categories');

            $table->integer('album_id')->nullable()->unsigned()->comment('ID альбома');
            $table->foreign('album_id')->references('id')->on('albums');


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
        Schema::dropIfExists('goods_products');
    }
}