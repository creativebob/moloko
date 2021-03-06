<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('lead_id')->unsigned()->nullable()->comment('Id лида');
            $table->foreign('lead_id')->references('id')->on('leads');

            $table->bigInteger('client_id')->unsigned()->nullable()->comment('Id клиента');
            $table->foreign('client_id')->references('id')->on('users');

	        $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
	        $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты');
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->text('description')->nullable()->comment('Описание');
            $table->date('date')->nullable()->comment('Дата сметы');
            $table->string('number')->nullable()->comment('Номер сметы');

            $table->boolean('is_main')->default(1)->comment('Главная');

            $table->boolean('is_dismissed')->default(0)->comment('Отменено');
            $table->bigInteger('cancel_ground_id')->nullable()->unsigned()->comment('Id основания списания');

            $table->integer('cost')->default(0)->comment('Себестоимость');
            $table->decimal('amount', 10, 2)->default(0)->comment('Сумма');

            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидки по прайсу');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидки по разделу каталога');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидки по смете');
            $table->decimal('client_discount', 10, 2)->default(0)->comment('Скидки клиента');
            $table->decimal('manual_discount', 10, 2)->default(0)->comment('Скидки ручные');

            $table->decimal('discount_currency', 10, 2)->default(0)->comment('Сумма скидок');
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Процент скидки');

            $table->decimal('certificate_amount', 10, 2)->default(0)->comment('Сумма оплаченная по сертификатам');

            $table->decimal('total', 10, 2)->default(0)->comment('Итоговая сумма по заказу');
            $table->integer('total_points')->default(0)->comment('Итого поинтами');
            $table->integer('total_bonuses')->default(0)->comment('Итого бонусами');

            $table->bigInteger('agent_id')->unsigned()->nullable()->comment('Id агента');
            $table->foreign('agent_id')->references('id')->on('agents');

            $table->bigInteger('agency_scheme_id')->unsigned()->nullable()->comment('Id агентской схемы');
            $table->foreign('agency_scheme_id')->references('id')->on('agency_schemes');

            $table->decimal('share_currency', 10,2)->default(0)->comment('Сумма агентсокго вознаграждения');
            $table->decimal('principal_currency', 10,2)->default(0)->comment('Сумма компании');

            $table->decimal('margin_percent', 10, 2)->default(0)->comment('Процент маржи');
            $table->decimal('margin_currency', 10, 2)->default(0)->comment('Сумма маржи');

            $table->decimal('surplus', 12, 4)->default(0)->comment('Излишек оплаты');
            $table->decimal('losses_from_points', 12, 4)->default(0)->comment('Потери от поинтов');

            $table->integer('points')->default(0)->comment('Внутренняя валюта');

            $table->decimal('paid', 10, 2)->default(0)->comment('Оплачено всего');
            $table->decimal('debit', 10, 2)->default(0)->comment('Долг');
            $table->string('payment_type')->nullable()->comment('Тип платежей');

            $table->boolean('draft')->default(0)->unsigned()->comment('Черновик');

            $table->timestamp('registered_at')->nullable()->comment('Время оформления');
            $table->timestamp('produced_at')->nullable()->comment('Время производства');
            $table->timestamp('conducted_at')->nullable()->comment('Время проведения');

            $table->integer('external')->nullable()->comment('Внешний id');

            $table->boolean('is_create_parse')->default(0)->comment('Создана парсером');

            $table->string('is_need_parse')->default(1)->comment('Нужно парсить');


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
        Schema::dropIfExists('estimates');
    }
}
