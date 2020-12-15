<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->morphs('clientable');

            $table->text('description')->nullable()->comment('Описание клиента');

            $table->bigInteger('loyalty_id')->nullable()->unsigned()->default(4)->comment('Id лояльности');
            $table->foreign('loyalty_id')->references('id')->on('loyalties');

            $table->boolean('is_lost')->default(0)->comment('Потерянный');
            $table->boolean('is_vip')->default(0)->comment('VIP-статус');
            $table->boolean('is_vip_abc')->default(0)->comment('VIP-статус по вычислениям');

            $table->bigInteger('source_id')->nullable()->unsigned()->comment('Id первого источника');
            $table->foreign('source_id')->references('id')->on('sources');

            $table->date('first_order_date')->nullable()->comment('Дата первого заказа');
            $table->date('last_order_date')->nullable()->comment('Дата последнего заказа');

            $table->bigInteger('lifetime')->default(0)->comment('Срок жизни');

            $table->integer('orders_count')->unsigned()->default(0)->comment('Кол-во заказов');
            $table->decimal('purchase_frequency', 6, 2)->default(0)->comment('Частота заказов');
            $table->decimal('ait', 6, 2)->default(0)->comment('Среднее время между покупками');
            $table->decimal('customer_equity', 12, 2)->default(0)->comment('Клиентский капитал');
            $table->decimal('average_order_value', 12, 2)->default(0)->comment('Средний чек');
            $table->decimal('customer_value', 12, 2)->default(0)->comment('Ценность клиента');
            $table->decimal('ltv', 12, 2)->default(0)->comment('Пожизненная ценность');

            $table->integer('use_promo_count')->unsigned()->default(0)->comment('Кол-во заказов по акции');
            $table->decimal('promo_rate', 6, 2)->default(0)->comment('Коэффициент использования промоакций');

            $table->smallInteger('rfm')->unsigned()->nullable()->comment('RFM-анализ');
            $table->char('abc', 1)->nullable()->comment('ABC-анализ');
            $table->char('xyz', 1)->nullable()->comment('XYZ-анализ');
            $table->char('abcxyz', 2)->nullable()->comment('Комбинация ABC и XYZ анализов');

            $table->char('activity', 4)->nullable()->comment('Динамика активности');

            $table->integer('discount')->default(0)->comment('Скидка');
            $table->integer('discount_dynamic')->default(0)->comment('Скидка динамическая');

            $table->integer('points')->default(0)->comment('Внутренняя валюта');

            $table->boolean('archive')->default(0)->comment('Статус архива');

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
        Schema::dropIfExists('clients');
    }
}
