<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsGoodsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_goods_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('catalogs_goods_id')->nullable()->unsigned()->comment('Id каталога');
            $table->foreign('catalogs_goods_id')->references('id')->on('catalogs_goods');

            $table->string('name')->index()->comment('Название');
            $table->string('slug')->index()->nullable()->comment('Слаг');

            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Id категории товара');
            $table->foreign('parent_id')->references('id')->on('catalogs_goods_items');

            $table->bigInteger('category_id')->unsigned()->nullable()->comment('Id категории, пишется каждому вложенному пункту');
            $table->foreign('category_id')->references('id')->on('catalogs_goods_items');


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
        Schema::dropIfExists('catalogs_goods_items');
    }
}
