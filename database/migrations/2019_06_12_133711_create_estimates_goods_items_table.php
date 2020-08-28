<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimatesGoodsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates_goods_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('estimate_id')->nullable()->unsigned()->comment('Id сметы');
            $table->foreign('estimate_id')->references('id')->on('estimates');

//            $table->morphs('product');
//            $table->morphs('price_product');

            $table->bigInteger('price_id')->unsigned()->nullable()->comment('Id прайса');
            $table->foreign('price_id')->references('id')->on('prices_goods');

            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты');
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->bigInteger('goods_id')->unsigned()->nullable()->comment('Id товара');
            $table->foreign('goods_id')->references('id')->on('goods');

            $table->tinyInteger('sale_mode')->default(1)->comment('Режим продажи: 1 - валюта, 2 - поинты');

            $table->bigInteger('stock_id')->unsigned()->nullable()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->integer('cost')->default(0)->comment('Себестоимость');
            $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим себестоимости');

            $table->decimal('price', 10,2)->default(0)->comment('Цена');
            $table->decimal('count', 10,2)->default(0)->comment('Количество');
            $table->decimal('amount', 10,2)->default(0)->comment('Сумма');

            $table->integer('points')->default(0)->comment('Внутренняя валюта');

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

            $table->decimal('margin_percent', 10,2)->default(0)->comment('Процент маржи');
            $table->decimal('margin_currency', 10,2)->default(0)->comment('Сумма маржи');

            $table->decimal('discount_percent', 10,2)->default(0)->comment('Процент скидки');
            $table->decimal('discount_currency', 10,2)->default(0)->comment('Сумма скидки');

            $table->decimal('extra_margin_percent', 10,2)->default(0)->comment('Общий процент маржи');
            $table->decimal('extra_margin_currency', 10,2)->default(0)->comment('Общая сумма маржи');

            $table->decimal('extra_discount_percent', 10,2)->default(0)->comment('Общий процент скидки');
            $table->decimal('extra_discount_currency', 10,2)->default(0)->comment('Общая сумма скидки');

            $table->decimal('total', 10,2)->default(0)->comment('Итоговая сумма');
            $table->integer('total_points')->default(0)->comment('Итого поинтами');
            $table->integer('total_bonuses')->default(0)->comment('Итого бонусами');

            $table->decimal('profit', 10,2)->default(0)->comment('Прибыль');

            $table->text('comment')->nullable()->comment('Комментарий');

            $table->boolean('is_reserved')->default(0)->comment('Зарезервировано');

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
        Schema::dropIfExists('estimates_goods_items');
    }
}
