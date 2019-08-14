<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices_goods', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('catalogs_goods_item_id')->nullable()->unsigned()->comment('Id пункта каталога');
            $table->foreign('catalogs_goods_item_id')->references('id')->on('catalogs_goods_items');

            $table->bigInteger('catalogs_goods_id')->nullable()->unsigned()->comment('Id каталога товаров');
            $table->foreign('catalogs_goods_id')->references('id')->on('catalogs_goods');

            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id товара');
            $table->foreign('goods_id')->references('id')->on('goods');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('ancestor_id')->nullable()->unsigned()->comment('Предок');
            $table->foreign('ancestor_id')->references('id')->on('prices_goods');

            $table->integer('price')->nullable()->comment('Цена');

            $table->boolean('archive')->default(0)->unsigned()->comment('Архив');

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
        Schema::dropIfExists('prices_goods');
    }
}