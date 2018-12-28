<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_rules', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            // $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->nullable()->comment('Имя ценовой политики');
            $table->text('description')->nullable()->comment('Описание ценовой политики');



            $table->boolean('margin_status')->comment('Статус наценки');
            $table->boolean('margin_priority')->default(true)->comment('Приоритет процента наценки');

            $table->decimal('margin_percent_min', 10, 2)->nullable()->default(0)->comment('Минимум (проценты) наценки');
            $table->decimal('margin_percent_max', 10, 2)->nullable()->default(10000)->comment('Максимум (проценты) наценки');
            $table->decimal('margin_percent_default', 10, 2)->nullable()->default(0)->comment('Умолчание (проценты) наценки');

            $table->decimal('margin_currency_min', 10, 2)->nullable()->default(0)->comment('Минимум (валюта) наценки');
            $table->decimal('margin_currency_max', 10, 2)->nullable()->default(10000000)->comment('Максимум (валюта) наценки');
            $table->decimal('margin_currency_default', 10, 2)->nullable()->default(0)->comment('Умолчание (валюта) наценки');




            $table->boolean('extra_margin_status')->comment('Статус дополнительной наценки');
            $table->boolean('extra_margin_priority')->default(true)->comment('Приоритет процента дополнительной наценки');

            $table->decimal('extra_margin_percent_min', 10, 2)->nullable()->default(0)->comment('Минимум (проценты) дополнительной наценки');
            $table->decimal('extra_margin_percent_max', 10, 2)->nullable()->default(10000)->comment('Максимум (проценты) дополнительной наценки');
            $table->decimal('extra_margin_percent_default', 10, 2)->nullable()->default(0)->comment('Умолчание (проценты) дополнительной наценки');

            $table->decimal('extra_margin_currency_min', 10, 2)->nullable()->default(0)->comment('Минимум (валюта) дополнительной наценки');
            $table->decimal('extra_margin_currency_max', 10, 2)->nullable()->default(10000000)->comment('Максимум (валюта) дополнительной наценки');
            $table->decimal('extra_margin_currency_default', 10, 2)->nullable()->default(0)->comment('Умолчание (валюта) дополнительной наценки');




            $table->boolean('discount_status')->comment('Статус скидки');
            $table->boolean('discount_priority')->default(true)->comment('Приоритет процента у скидки');

            $table->decimal('discount_percent_min', 10, 2)->nullable()->default(0)->comment('Минимум (проценты) скидки');
            $table->decimal('discount_percent_max', 10, 2)->nullable()->default(10000)->comment('Максимум (проценты) скидки');
            $table->decimal('discount_percent_default', 10, 2)->nullable()->default(0)->comment('Умолчание (проценты) скидки');

            $table->decimal('discount_currency_min', 10, 2)->nullable()->default(0)->comment('Минимум (валюта) скидки');
            $table->decimal('discount_currency_max', 10, 2)->nullable()->default(10000000)->comment('Максимум (валюта) скидки');
            $table->decimal('discount_currency_default', 10, 2)->nullable()->default(0)->comment('Умолчание (валюта) скидки');




            $table->boolean('extra_discount_status')->comment('Статус дополнительной наценки');
            $table->boolean('extra_discount_priority')->default(true)->comment('Приоритет процента');
            
            $table->decimal('extra_discount_percent_min', 10, 2)->nullable()->default(0)->comment('Минимум (проценты) дополнительной скидки');
            $table->decimal('extra_discount_percent_max', 10, 2)->nullable()->default(10000)->comment('Максимум (проценты) дополнительной скидки');
            $table->decimal('extra_discount_percent_default', 10, 2)->nullable()->default(0)->comment('Умолчание (проценты) дополнительной скидки');

            $table->decimal('extra_discount_currency_min', 10, 2)->nullable()->default(0)->comment('Минимум (валюта) дополнительной скидки');
            $table->decimal('extra_discount_currency_max', 10, 2)->nullable()->default(10000000)->comment('Максимум (валюта) дополнительной скидки');
            $table->decimal('extra_discount_currency_default', 10, 2)->nullable()->default(0)->comment('Умолчание (валюта) дополнительной скидки');




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


    public function down()
    {
        Schema::dropIfExists('price_rules');
    }
}
