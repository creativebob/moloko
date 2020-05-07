<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_indicators', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('start_date')->nullable()->comment('Дата старта вычислений');

            $table->bigInteger('unit_id')->unsigned()->nullable()->comment('Id юнита');
//            $table->foreign('unit_id')->references('id')->on('units');

            $table->integer('count')->unsigned()->default(0)->comment('Общее количество контактов');
            $table->integer('active_previous_count')->unsigned()->default(0)->comment('Количество "Действующих" клиентов на начало периода');
            $table->integer('active_count')->unsigned()->default(0)->comment('Количество "Действующих" клиентов');
            $table->integer('lost_count')->unsigned()->default(0)->comment('Количество "Потерянных" клиентов');
            $table->integer('deleted_count')->unsigned()->default(0)->comment('Количество исключеных из базы');
            $table->integer('blacklist_count')->unsigned()->default(0)->comment('Количество контактов в черном списке');

            $table->integer('new_clients_period_count')->unsigned()->default(0)->comment('Кол-во новых клиентов в периоде');
            $table->integer('lost_clients_period_count')->unsigned()->default(0)->comment('Кол-во ушедших клиентов в периоде');

            $table->decimal('customer_retention_rate', 7, 4)->default(0)->comment('Коэффициент удержания');
            $table->integer('orders_count')->unsigned()->default(0)->comment('Оформленные сметы');
            $table->integer('orders_period_count')->unsigned()->default(0)->comment('Оформленные сметы за период');
            $table->decimal('churn_rate', 7, 4)->default(0)->comment('Коэффициент оттока');

            $table->integer('customers_period_count')->unsigned()->default(0)->comment('Количество покупателей из числа клиентов в периоде');
            $table->decimal('lead_close_rate', 7, 4)->default(0)->comment('Коэффициент закрытия лидов');
            $table->decimal('repeat_purchase_rate', 7, 4)->default(0)->comment('Коэффициент удовлетворенности (Коэффициент повторных покупок)');

            $table->decimal('purchase_frequency', 6, 2)->default(0)->comment('Частота заказов');
            $table->decimal('purchase_frequency_period', 6, 2)->default(0)->comment('Частота заказов за период');

            $table->decimal('order_gap_analysis', 6, 2)->default(0)->comment('Средний промежуток времени между покупками');
            $table->decimal('orders_revenue', 12, 2)->default(0)->comment('Общая выручка');
            $table->decimal('orders_revenue_period', 12, 2)->default(0)->comment('Общая выручка за период');

            $table->decimal('arpu', 12, 2)->default(0)->comment('Средний доход от клиента за период');
            $table->decimal('arppu', 12, 2)->default(0)->comment('Средний доход от платящего клиента за период');
            $table->decimal('paying_share', 6, 2)->default(0)->comment('Доля платящих клиентов');

            $table->bigInteger('lifetime')->default(0)->comment('Срок жизни по методике');
            $table->bigInteger('lifetime_fact')->default(0)->comment('Срок жизни по нашим расчетам');

            $table->decimal('average_order_value', 12, 2)->default(0)->comment('Средний чек');
            $table->decimal('average_order_value_period', 12, 2)->default(0)->comment('Средний чек за период');

            $table->decimal('customer_value', 12, 2)->default(0)->comment('Ценность клиента');
            $table->decimal('customer_value_period', 12, 2)->default(0)->comment('Ценность клиента за период');
            $table->decimal('ltv', 12, 2)->default(0)->comment('Пожизненная ценность');
            $table->decimal('ltv_period', 12, 2)->default(0)->comment('Пожизненная ценность на основе данных за период');
            $table->decimal('customer_equity', 12, 2)->default(0)->comment('Клиентский капитал');

            $table->tinyInteger('nps')->unsigned()->nullable()->comment('Индекс лояльности');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
//            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(1)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
//            $table->foreign('author_id')->references('id')->on('users');

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
        Schema::dropIfExists('clients_indicators');
    }
}
