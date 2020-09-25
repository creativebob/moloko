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

            $table->integer('cost')->default(0)->comment('Себестоимость');
            $table->decimal('amount', 10, 2)->default(0)->comment('Сумма');

            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидки по прайсу');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидкки по разделу каталога');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидкки по смете');
            $table->decimal('client_discount', 10, 2)->default(0)->comment('Скидки клиента');
            $table->decimal('manual_discount', 10, 2)->default(0)->comment('Скидки ручная');

            $table->decimal('discount_currency', 10, 2)->default(0)->comment('Сумма скидок');
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Процент скидки');

            $table->decimal('certificate_amount', 10, 2)->default(0)->comment('Сумма оплаченная по сертификатам');

            $table->decimal('total', 10, 2)->default(0)->comment('Итоговая сумма по заказу');
            $table->integer('total_points')->default(0)->comment('Итого поинтами');
            $table->integer('total_bonuses')->default(0)->comment('Итого бонусами');

            $table->decimal('margin_percent', 10, 2)->default(0)->comment('Процент маржи');
            $table->decimal('margin_currency', 10, 2)->default(0)->comment('Сумма маржи');

            $table->decimal('surplus', 12, 4)->default(0)->comment('Излишек оплаты');
            $table->decimal('losses_from_points', 12, 4)->default(0)->comment('Потери от поинтов');

            $table->integer('points')->default(0)->comment('Внутренняя валюта');

            $table->boolean('draft')->default(0)->unsigned()->comment('Черновик');

            $table->boolean('is_registered')->default(0)->comment('Оформлено');
            $table->date('registered_date')->nullable()->comment('Дата оформления');

            $table->boolean('is_saled')->default(0)->comment('Продано');
            $table->date('saled_date')->nullable()->comment('Дата продажи');

            $table->boolean('is_produced')->default(0)->comment('Произведено');

            $table->integer('external')->nullable()->comment('Внешний id');

            $table->boolean('is_create_parse')->default(0)->comment('Создана парсером');


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
