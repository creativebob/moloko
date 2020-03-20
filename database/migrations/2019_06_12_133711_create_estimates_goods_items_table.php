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

            $table->bigInteger('stock_id')->unsigned()->nullable()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->integer('cost')->nullable()->comment('Себестоимость');
            $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим мебестоимости');

            $table->decimal('price', 12, 4)->default(0)->comment('Цена');
            $table->decimal('count', 12,4)->default(0)->comment('Количество');
            $table->decimal('amount', 12, 4)->nullable()->comment('Сумма');

            $table->decimal('margin_percent', 10, 2)->nullable()->comment('Процент маржи');
            $table->decimal('margin_currency', 10, 2)->nullable()->comment('Сумма маржи');

            $table->decimal('discount_percent', 10, 2)->nullable()->comment('Процент скидки');
            $table->decimal('discount_currency', 10, 2)->nullable()->comment('Сумма скидки');

            $table->decimal('extra_margin_percent', 10, 2)->nullable()->comment('Общий процент маржи');
            $table->decimal('extra_margin_currency', 10, 2)->nullable()->comment('Общая сумма маржи');

            $table->decimal('extra_discount_percent', 10, 2)->nullable()->comment('Общий процент скидки');
            $table->decimal('extra_discount_currency', 10, 2)->nullable()->comment('Общая сумма скидки');

            $table->decimal('total', 12, 4)->nullable()->comment('Итоговая сумма');
            $table->decimal('profit', 12, 4)->nullable()->comment('Прибыль');

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
