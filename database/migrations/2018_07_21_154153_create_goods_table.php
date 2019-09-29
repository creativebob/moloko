<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('article_id')->nullable()->unsigned()->comment('Id артикула');
            $table->foreign('article_id')->references('id')->on('articles');

            $table->bigInteger('category_id')->nullable()->unsigned()->comment('Id категории товаров');
            $table->foreign('category_id')->references('id')->on('goods_categories');

            $table->bigInteger('price_unit_category_id')->nullable()->unsigned()->comment('Категория единицы измерения для определения цены');
            $table->foreign('price_unit_category_id')->references('id')->on('units_categories');

            $table->bigInteger('price_unit_id')->nullable()->unsigned()->comment('Единица измерения для определения цены');
            $table->foreign('price_unit_id')->references('id')->on('units');

            $table->boolean('archive')->default(0)->unsigned()->comment('Статус архива');
            $table->boolean('serial')->default(0)->unsigned()->comment('Серийный номер');

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
        Schema::dropIfExists('goods');
    }
}
