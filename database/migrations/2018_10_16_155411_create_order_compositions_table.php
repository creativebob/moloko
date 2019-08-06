<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_compositions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('order_id')->nullable()->unsigned()->comment('ID заказа');
            $table->foreign('order_id')->references('id')->on('orders');

            $table->morphs('product');

            $table->integer('count')->nullable()->unsigned()->comment('Количество товаров');

            // $table->integer('cost')->nullable()->comment('Себестоимость');
            // $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим мебестоимости');



            // $table->decimal('margin_percent', 10, 2)->nullable()->comment('Процент маржи');
            // $table->decimal('margin_currency', 10, 2)->nullable()->comment('Сумма маржи');

            // $table->decimal('discount_percent', 10, 2)->nullable()->comment('Процент скидки');
            // $table->decimal('discount_currency', 10, 2)->nullable()->comment('Сумма скидки');

            // $table->decimal('extra_margin_percent', 10, 2)->nullable()->comment('Общий процент маржи');
            // $table->decimal('extra_margin_currency', 10, 2)->nullable()->comment('Общая сумма маржи');

            // $table->decimal('extra_discount_percent', 10, 2)->nullable()->comment('Общий процент скидки');
            // $table->decimal('extra_discount_currency', 10, 2)->nullable()->comment('Общая сумма скидки');

            $table->decimal('sum', 10, 2)->nullable()->comment('Сумма');
            $table->decimal('total', 10, 2)->nullable()->comment('Итоговая сумма');
            // $table->decimal('profit', 10, 2)->nullable()->comment('прибыль');


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
        Schema::dropIfExists('order_compositions');
    }
}
