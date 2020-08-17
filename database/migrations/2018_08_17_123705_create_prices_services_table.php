<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('catalogs_services_item_id')->nullable()->unsigned()->comment('Id пункта каталога');
            $table->foreign('catalogs_services_item_id')->references('id')->on('catalogs_services_items');

            $table->bigInteger('catalogs_service_id')->nullable()->unsigned()->comment('Id каталога услуг');
            $table->foreign('catalogs_service_id')->references('id')->on('catalogs_services');

            $table->bigInteger('service_id')->nullable()->unsigned()->comment('Id услуги');
            $table->foreign('service_id')->references('id')->on('services');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('ancestor_id')->nullable()->unsigned()->comment('Предок');
            $table->foreign('ancestor_id')->references('id')->on('prices_services');

            $table->decimal('price', 10,2)->default(0)->comment('Цена');

            $table->tinyInteger('discount_mode')->unsigned()->default(1)->comment('Тип скидки: 1 - проценты, 2 - валюта');
            $table->decimal('discount_percent', 10,2)->default(0)->comment('Процент скидки');
            $table->decimal('discount_currency', 10,2)->default(0)->comment('Сумма скидки');

            $table->decimal('total', 10,2)->default(0)->comment('Итоговая сумма');

            $table->integer('points')->default(0)->comment('Внутренняя валюта');
//            $table->decimal('point', 12, 4)->default(0)->comment('Внутренняя валюта');

            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты');
            $table->foreign('currency_id')->references('id')->on('currencies');

            $table->boolean('archive')->default(0)->unsigned()->comment('Архив');

            $table->boolean('status')->default(0)->comment('Статус');
            $table->boolean('is_hit')->default(0)->comment('Хит');
            $table->boolean('is_new')->default(0)->comment('Новинка');

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
        Schema::dropIfExists('prices_services');
    }
}
