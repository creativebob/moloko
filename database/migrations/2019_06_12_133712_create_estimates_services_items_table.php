<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimatesServicesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates_services_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('estimate_id')->nullable()->unsigned()->comment('Id сметы');
            $table->foreign('estimate_id')->references('id')->on('estimates');

            $table->bigInteger('price_id')->unsigned()->nullable()->comment('Id прайса');
            $table->foreign('price_id')->references('id')->on('prices_services');

            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты');
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->bigInteger('service_id')->unsigned()->nullable()->comment('Id услуги');
            $table->foreign('service_id')->references('id')->on('services');

            $table->tinyInteger('sale_mode')->default(1)->comment('Режим продажи: 1 - валюта, 2 - поинты');

            $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим себестоимости');
            $table->decimal('cost_unit', 10,2)->default(0)->comment('Себестоимость за единицу');
            $table->decimal('cost', 10,2)->default(0)->comment('Себестоимость');

            $table->decimal('price', 10,2)->default(0)->comment('Цена');
            $table->integer('points')->default(0)->comment('Внутренняя валюта');
            $table->decimal('count', 10,2)->default(0)->comment('Количество');
            $table->decimal('amount', 10,2)->default(0)->comment('Сумма');

            $table->bigInteger('price_discount_id')->nullable()->unsigned()->comment('Id скидки прайса');
            $table->foreign('price_discount_id')->references('id')->on('discounts');
            $table->decimal('price_discount_unit', 10, 2)->default(0)->comment('Скидка по прайсу за единицу');
            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидка по прайсу');
            $table->decimal('total_price_discount', 10, 2)->default(0)->comment('Сумма с скидкой по прайсу');

            $table->bigInteger('catalogs_item_discount_id')->nullable()->unsigned()->comment('Id скидки раздела каталога');
            $table->foreign('catalogs_item_discount_id')->references('id')->on('discounts');
            $table->decimal('catalogs_item_discount_unit', 10, 2)->default(0)->comment('Скидкка по разделу каталога за единицу');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидкка по разделу каталога');
            $table->decimal('total_catalogs_item_discount', 10, 2)->default(0)->comment('Сумма с скидкой по разделу каталога');

            $table->bigInteger('estimate_discount_id')->nullable()->unsigned()->comment('Id скидки сметы');
            $table->foreign('estimate_discount_id')->references('id')->on('discounts');
            $table->decimal('estimate_discount_unit', 10, 2)->default(0)->comment('Скидкка по смете за единицу');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидкка по смете');
            $table->decimal('total_estimate_discount', 10, 2)->default(0)->comment('Сумма с скидкой по смете');

            $table->decimal('client_discount_percent', 10, 2)->default(0)->comment('Скидка клиента (%)');
            $table->decimal('client_discount_unit_currency', 10, 2)->default(0)->comment('Скидка клиента за единицу (валюта)');
            $table->decimal('client_discount_currency', 10, 2)->default(0)->comment('Скидка клиента (валюта)');
            $table->decimal('total_client_discount', 10, 2)->default(0)->comment('Сумма со скидкой клиента');

            $table->decimal('computed_discount_percent', 10, 2)->default(0)->comment('Высчитанная скидка (%)');
            $table->decimal('computed_discount_currency', 10, 2)->default(0)->comment('Высчитанная скидка (валюта)');
            $table->decimal('total_computed_discount', 10, 2)->default(0)->comment('Сумма с высчитанной скидкой');

            $table->boolean('is_manual')->default(0)->comment('Ручной режим скидки');
            $table->decimal('manual_discount_percent', 10, 2)->default(0)->comment('Ручная скидка (%)');
            $table->decimal('manual_discount_currency', 10, 2)->default(0)->comment('Ручная скидка (валюта)');
            $table->decimal('total_manual_discount', 10, 2)->default(0)->comment('Сумма с ручной скидкой');

            $table->decimal('discount_percent', 10,2)->default(0)->comment('Процент скидки');
            $table->decimal('discount_currency', 10,2)->default(0)->comment('Сумма скидки');

//            $table->decimal('extra_margin_percent', 10,2)->default(0)->comment('Общий процент маржи');
//            $table->decimal('extra_margin_currency', 10,2)->default(0)->comment('Общая сумма маржи');
//
//            $table->decimal('extra_discount_percent', 10,2)->default(0)->comment('Общий процент скидки');
//            $table->decimal('extra_discount_currency', 10,2)->default(0)->comment('Общая сумма скидки');

            $table->decimal('total', 10,2)->default(0)->comment('Итоговая сумма');
            $table->integer('total_points')->default(0)->comment('Итого поинтами');
            $table->integer('total_bonuses')->default(0)->comment('Итого бонусами');

            $table->bigInteger('agent_id')->unsigned()->nullable()->comment('Id агента');
            $table->foreign('agent_id')->references('id')->on('agents');

            $table->bigInteger('agency_scheme_id')->unsigned()->nullable()->comment('Id агентской схемы');
            $table->foreign('agency_scheme_id')->references('id')->on('agency_schemes');

            $table->decimal('share_percent', 5,2)->default(0)->comment('Процент агентсокго вознаграждения');
            $table->decimal('share_currency', 10,2)->default(0)->comment('Сумма агентсокго вознаграждения');
            $table->decimal('principal_currency', 10,2)->default(0)->comment('Сумма компании');

            $table->decimal('margin_currency_unit', 10,2)->default(0)->comment('Процент маржи за единицу');
            $table->decimal('margin_percent_unit', 10,2)->default(0)->comment('Сумма маржи за единицу');
            $table->decimal('margin_percent', 10,2)->default(0)->comment('Процент маржи');
            $table->decimal('margin_currency', 10,2)->default(0)->comment('Сумма маржи');

            $table->decimal('profit', 10,2)->default(0)->comment('Прибыль');

            $table->text('comment')->nullable()->comment('Комментарий');

            $table->bigInteger('flow_id')->unsigned()->nullable()->comment('Id потока');
//            $table->foreign('flow_id')->references('id')->on('services_flows');

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
        Schema::dropIfExists('estimates_services_items');
    }
}
