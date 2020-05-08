<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update210420Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->tinyInteger('loyality_score')->unsigned()->nullable()->comment('Пользовательская оценка')->after('loyality_id');

            $table->boolean('is_lost')->default(0)->comment('Потерянный')->after('loyalty_id');
            $table->boolean('is_vip')->default(0)->comment('VIP-статус')->after('is_lost');
            $table->boolean('is_blacklist')->default(0)->comment('В черном списке')->after('is_vip');

            $table->bigInteger('source_id')->nullable()->unsigned()->default(4)->comment('Id первого источника')->after('is_blacklist');
            $table->foreign('source_id')->references('id')->on('sources');

            $table->date('first_order_date')->nullable()->comment('Дата первого заказа')->after('source_id');
            $table->date('last_order_date')->nullable()->comment('Дата последнего заказа')->after('first_order_date');

            $table->bigInteger('lifetime')->default(0)->comment('Срок жизни')->after('last_order_date');

            $table->integer('orders_count')->unsigned()->default(0)->comment('Кол-во заказов')->after('lifetime');
            $table->decimal('purchase_frequency', 6, 2)->default(0)->comment('Частота заказов')->after('orders_count');
            $table->decimal('ait', 6, 2)->default(0)->comment('Среднее время между покупками')->after('purchase_frequency');
            $table->decimal('customer_equity', 12, 2)->default(0)->comment('Клиентский капитал')->after('ait');
            $table->decimal('average_order_value', 12, 2)->default(0)->comment('Средний чек')->after('customer_equity');
            $table->decimal('customer_value', 12, 2)->default(0)->comment('Ценность клиента')->after('average_order_value');
            $table->decimal('ltv', 12, 2)->default(0)->comment('Пожизненная ценность')->after('customer_value');

            $table->integer('use_promo_count')->unsigned()->default(0)->comment('Кол-во заказов по акции')->after('ltv');
            $table->decimal('promo_rate', 6, 2)->default(0)->comment('Коэффициент использования промоакций')->after('use_promo_count');

            $table->smallInteger('rfm')->unsigned()->nullable()->comment('RFM-анализ')->after('promo_rate');
            $table->char('abc', 1)->nullable()->comment('ABC-анализ')->after('rfm');
            $table->char('xyz', 1)->nullable()->comment('XYZ-анализ')->after('abc');
            $table->char('abcxyz', 2)->nullable()->comment('Комбинация ABC и XYZ анализов')->after('xyz');

            $table->char('activity', 4)->nullable()->comment('Динамика активности')->after('abcxyz');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->integer('cost')->default(0)->comment('Себестоимость')->after('number');
            $table->decimal('margin_percent', 10, 2)->default(0)->comment('Процент маржи')->after('total');
            $table->decimal('margin_currency', 10, 2)->default(0)->comment('Сумма маржи')->after('margin_percent');

            $table->date('registered_date')->nullable()->comment('Дата оформления')->after('is_registered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {

            $table->dropForeign('clients_source_id_foreign');

            $table->dropColumn([
                'is_lost',
                'is_vip',
                'is_blacklist',
                'source_id',
                'first_order_date',
                'last_order_date',
                'lifetime',
                'orders_count',
                'purchase_frequency',
                'ait',
                'customer_equity',
                'average_order_value',
                'customer_value',
                'ltv',
                'use_promo_count',
                'promo_rate',
                'loyality_score',
                'rfm',
                'abc',
                'xyz',
                'abcxyz',
                'activity',
            ]);
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn([
                'cost',
                'margin_percent',
                'margin_currency',
                'is_registered',
            ]);
        });
    }
}
