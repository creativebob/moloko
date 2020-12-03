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

            $table->decimal('price', 10,2)->default(0)->comment('Цена');
            $table->string('name_alt')->nullable()->comment('Альтернативное имя');
            $table->string('external')->nullable()->comment('Внешний ID');

            $table->boolean('is_discount')->default(1)->unsigned()->comment('Режим скидок');

            $table->tinyInteger('discount_mode')->unsigned()->default(1)->comment('Тип скидки: 1 - проценты, 2 - валюта');
            $table->decimal('discount_percent', 10,2)->default(0)->comment('Процент скидки');
            $table->decimal('discount_currency', 10,2)->default(0)->comment('Сумма скидки');

            $table->bigInteger('price_discount_id')->nullable()->unsigned()->comment('Id скидки прайса');
            $table->foreign('price_discount_id')->references('id')->on('discounts');
            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидка по прайсу');
            $table->decimal('total_price_discount', 10, 2)->default(0)->comment('Сумма с скидкой по прайсу');

            $table->bigInteger('catalogs_item_discount_id')->nullable()->unsigned()->comment('Id скидки раздела каталога');
            $table->foreign('catalogs_item_discount_id')->references('id')->on('discounts');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидкка по разделу каталога');
            $table->decimal('total_catalogs_item_discount', 10, 2)->default(0)->comment('Сумма с скидкой по разделу каталога');

            $table->bigInteger('estimate_discount_id')->nullable()->unsigned()->comment('Id скидки сметы');
            $table->foreign('estimate_discount_id')->references('id')->on('discounts');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидкка по смете');
            $table->decimal('total_estimate_discount', 10, 2)->default(0)->comment('Сумма с скидкой по смете');

            $table->decimal('total', 10, 2)->default(0)->comment('Итоговая сумма');

            $table->integer('points')->default(0)->comment('Внутренняя валюта');

            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты');
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->boolean('archive')->default(0)->unsigned()->comment('Архив');

	        $table->boolean('status')->default(0)->comment('Статус');
            $table->boolean('is_hit')->default(0)->comment('Хит');
            $table->boolean('is_new')->default(0)->comment('Новинка');
            $table->boolean('is_priority')->default(0)->comment('Приоритет продажи');
            $table->boolean('is_preorder')->default(0)->comment('Продажа по предзаказу');

            $table->boolean('is_show_price')->default(0)->comment('Показывать цену');
            $table->boolean('is_need_recalculate')->default(0)->comment('Требуется перерасчет');

            $table->boolean('is_exported_to_market')->default(0)->comment('Выгружать во внешние магазины');

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
        Schema::dropIfExists('prices_goods');
    }
}
