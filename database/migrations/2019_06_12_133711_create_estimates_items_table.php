<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimatesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('estimate_id')->nullable()->unsigned()->comment('Id сметы');
            $table->foreign('estimate_id')->references('id')->on('estimates');

            $table->bigInteger('service_id')->nullable()->unsigned()->comment('Id услуги');
            $table->foreign('service_id')->references('id')->on('services');

            $table->morphs('product');

            $table->morphs('price');

            $table->integer('cost')->nullable()->comment('Себестоимость');
            $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим мебестоимости');

            $table->integer('price')->default(0)->comment('Цена');
            $table->integer('count')->default(0)->comment('Количество');

            $table->decimal('margin_percent', 10, 2)->nullable()->comment('Процент маржи');
            $table->decimal('margin_currency', 10, 2)->nullable()->comment('Сумма маржи');

            $table->decimal('discount_percent', 10, 2)->nullable()->comment('Процент скидки');
            $table->decimal('discount_currency', 10, 2)->nullable()->comment('Сумма скидки');

            $table->decimal('extra_margin_percent', 10, 2)->nullable()->comment('Общий процент маржи');
            $table->decimal('extra_margin_currency', 10, 2)->nullable()->comment('Общая сумма маржи');

            $table->decimal('extra_discount_percent', 10, 2)->nullable()->comment('Общий процент скидки');
            $table->decimal('extra_discount_currency', 10, 2)->nullable()->comment('Общая сумма скидки');

            $table->decimal('sum', 10, 2)->nullable()->comment('Сумма');
            $table->decimal('total', 10, 2)->nullable()->comment('Итоговая сумма');
            $table->decimal('profit', 10, 2)->nullable()->comment('прибыль');

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
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estimates_items');
    }
}
