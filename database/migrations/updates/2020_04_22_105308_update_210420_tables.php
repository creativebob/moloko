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
            $table->boolean('is_lost')->default(0)->comment('Потерянный')->after('loyalty_id');
            $table->boolean('is_vip')->default(0)->comment('VIP-статус')->after('is_lost');
            $table->boolean('is_vip_abc')->default(0)->comment('VIP-статус по вычислениям')->after('is_vip');

            $table->bigInteger('source_id')->nullable()->unsigned()->comment('Id первого источника')->after('is_vip_abc');
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

            $table->integer('discount')->default(0)->comment('Скидка')->after('activity');
            $table->integer('discount_dynamic')->default(0)->comment('Скидка динамическая')->after('discount');
            $table->integer('points')->default(0)->comment('Внутренняя валюта')->after('discount_dynamic');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->default(0)->comment('Себестоимость')->after('number');
            $table->decimal('margin_percent', 10, 2)->default(0)->comment('Процент маржи')->after('total');
            $table->decimal('margin_currency', 10, 2)->default(0)->comment('Сумма маржи')->after('margin_percent');
            $table->integer('points')->default(0)->comment('Внутренняя валюта')->after('margin_percent');

            $table->decimal('discount_items_currency', 10, 2)->default(0)->comment('Сумма скидки по позициям')->after('points');

            $table->decimal('certificate_amount', 12, 2)->default(0)->comment('Сумма оплаченная по сертификатам')->after('discount_items_currency');

            $table->decimal('surplus', 12, 4)->default(0)->comment('Излишек оплаты')->after('certificate_amount');
            $table->decimal('losses_from_points', 12, 4)->default(0)->comment('Потери от поинтов')->after('surplus');

            $table->date('registered_date')->nullable()->comment('Дата оформления')->after('is_registered');

            $table->boolean('is_main')->default(1)->comment('Главная')->after('registered_date');

            $table->date('saled_date')->nullable()->comment('Дата продажи')->after('is_saled');

            $table->boolean('is_dismissed')->default(0)->comment('Отменено')->after('is_main');

            $table->integer('external')->default(0)->comment('Внешний id')->after('is_dismissed');

            $table->boolean('is_create_parse')->default(0)->comment('Создана парсером')->after('external');

            $table->integer('total_points')->default(0)->comment('Итого поинтами')->after('total');
            $table->integer('total_bonuses')->default(0)->comment('Итого бонусами')->after('total_points');
        });

        Schema::table('estimates_goods_items', function (Blueprint $table) {
            $table->integer('points')->default(0)->comment('Внутренняя валюта')->after('amount');

            $table->bigInteger('price_discount_id')->nullable()->unsigned()->comment('Id скидки прайса')->after('amount');
//            $table->foreign('price_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидка по прайсу')->after('price_discount_id');
            $table->decimal('total_price_discount', 10, 2)->default(0)->comment('Сумма с скидкой по прайсу')->after('price_discount');

            $table->bigInteger('catalogs_item_discount_id')->nullable()->unsigned()->comment('Id скидки раздела каталога')->after('total_price_discount');
//            $table->foreign('catalogs_item_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидкка по разделу каталога')->after('catalogs_item_discount_id');
            $table->decimal('total_catalogs_item_discount', 10, 2)->default(0)->comment('Сумма с скидкой по разделу каталога')->after('catalogs_item_discount');

            $table->bigInteger('estimate_discount_id')->nullable()->unsigned()->comment('Id скидки сметы')->after('total_catalogs_item_discount');
//            $table->foreign('estimate_discount_id')->references('id')->on('discounts');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидкка по смете')->after('estimate_discount_id');
            $table->decimal('total_estimate_discount', 10, 2)->default(0)->comment('Сумма с скидкой по смете')->after('estimate_discount');

            $table->text('comment')->nullable()->comment('Комментарий')->after('profit');
            $table->tinyInteger('sale_mode')->default(1)->comment('Режим продажи: 1 - валюта, 2 - поинты')->after('goods_id');
            $table->integer('total_points')->default(0)->comment('Итого поинтами')->after('total');
            $table->integer('total_bonuses')->default(0)->comment('Итого бонусами')->after('total_points');

        });

        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->integer('points')->default(0)->comment('Внутренняя валюта')->after('amount');

            $table->bigInteger('price_discount_id')->nullable()->unsigned()->comment('Id скидки прайса')->after('amount');
//            $table->foreign('price_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидка по прайсу')->after('price_discount_id');
            $table->decimal('total_price_discount', 10, 2)->default(0)->comment('Сумма с скидкой по прайсу')->after('price_discount');

            $table->bigInteger('catalogs_item_discount_id')->nullable()->unsigned()->comment('Id скидки раздела каталога')->after('total_price_discount');
//            $table->foreign('catalogs_item_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидкка по разделу каталога')->after('catalogs_item_discount_id');
            $table->decimal('total_catalogs_item_discount', 10, 2)->default(0)->comment('Сумма с скидкой по разделу каталога')->after('catalogs_item_discount');

            $table->bigInteger('estimate_discount_id')->nullable()->unsigned()->comment('Id скидки сметы')->after('total_catalogs_item_discount');
//            $table->foreign('estimate_discount_id')->references('id')->on('discounts');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидкка по смете')->after('estimate_discount_id');
            $table->decimal('total_estimate_discount', 10, 2)->default(0)->comment('Сумма с скидкой по смете')->after('estimate_discount');

            $table->text('comment')->nullable()->comment('Комментарий')->after('profit');
            $table->tinyInteger('sale_mode')->default(1)->comment('Режим продажи: 1 - валюта, 2 - поинты')->after('service_id');
            $table->integer('total_points')->default(0)->comment('Итого поинтами')->after('total');
            $table->integer('total_bonuses')->default(0)->comment('Итого бонусами')->after('total_points');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('birthday_company');
            $table->date('foundation_date')->nullable()->comment('Дата основания')->after('seo_description');
        });

        Schema::table('widgets', function (Blueprint $table) {
            $table->dropForeign('widgets_company_id_foreign');
            $table->dropForeign('widgets_author_id_foreign');

            $table->dropColumn([
                'company_id',
                'sort',
                'display',
                'system',
                'moderation',
                'author_id',
                'editor_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->char('mode', 11)->comment('Режим')->after('end_date');
            $table->text('horizontal')->nullable()->comment('horizontal')->after('large_x_id');
            $table->text('vertical')->nullable()->comment('vertical')->after('horizontal');
            $table->text('square')->nullable()->comment('square')->after('vertical');
            $table->string('prom')->nullable()->comment('Триггер для отображения')->after('square');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('is_create_parse')->default(0)->comment('Создан парсером')->after('delivered_at');
            $table->boolean('is_link_parse')->default(0)->comment('Связан парсером со сметой')->after('is_create_parse');
            $table->decimal('order_amount_base', 12, 4)->default(0)->comment('Сумма первоначального заказа')->after('is_link_parse');
            $table->boolean('need_delivery')->default(0)->comment('Нужна доставка')->after('order_amount_base');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('login_date')->nullable()->comment('Последняя авторизация')->after('login');
            $table->integer('external')->nullable()->comment('Внешний id')->after('filial_id');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->text('video')->nullable()->comment('Видео')->after('video_url');
        });

        Schema::table('catalogs_goods_items', function (Blueprint $table) {
            $table->string('header')->nullable()->comment('заголовок')->after('level');
            $table->boolean('is_discount')->default(1)->unsigned()->comment('Режим скидок')->after('color');
            $table->boolean('is_hide_submenu')->default(0)->comment('Не отображать субменю')->after('is_show_subcategory');
        });

        Schema::table('catalogs_services_items', function (Blueprint $table) {
            $table->string('header')->nullable()->comment('заголовок')->after('level');
            $table->boolean('is_discount')->default(1)->unsigned()->comment('Режим скидок')->after('color');
            $table->boolean('is_hide_submenu')->default(0)->comment('Не отображать субменю')->after('is_show_subcategory');
        });

        Schema::table('prices_goods', function (Blueprint $table) {
            $table->boolean('is_discount')->default(1)->unsigned()->comment('Режим скидок')->after('price');

            $table->tinyInteger('discount_mode')->unsigned()->default(1)->comment('Тип скидки: 1 - проценты, 2 - валюта')->after('is_discount');
            $table->decimal('discount_percent', 10,2)->default(0)->comment('Процент скидки')->after('discount_mode');
            $table->decimal('discount_currency', 10,2)->default(0)->comment('Сумма скидки')->after('discount_percent');

            $table->bigInteger('price_discount_id')->nullable()->unsigned()->comment('Id скидки прайса')->after('discount_currency');
//            $table->foreign('price_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидка по прайсу')->after('price_discount_id');
            $table->decimal('total_price_discount', 10, 2)->default(0)->comment('Сумма с скидкой по прайсу')->after('price_discount');

            $table->bigInteger('catalogs_item_discount_id')->nullable()->unsigned()->comment('Id скидки раздела каталога')->after('total_price_discount');
//            $table->foreign('catalogs_item_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидкка по разделу каталога')->after('catalogs_item_discount_id');
            $table->decimal('total_catalogs_item_discount', 10, 2)->default(0)->comment('Сумма с скидкой по разделу каталога')->after('catalogs_item_discount');

            $table->bigInteger('estimate_discount_id')->nullable()->unsigned()->comment('Id скидки сметы')->after('total_catalogs_item_discount');
//            $table->foreign('estimate_discount_id')->references('id')->on('discounts');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидкка по смете')->after('estimate_discount_id');
            $table->decimal('total_estimate_discount', 10, 2)->default(0)->comment('Сумма с скидкой по смете')->after('estimate_discount');

            $table->decimal('total', 12,2)->default(0)->comment('Итоговая сумма')->after('total_estimate_discount');

            $table->boolean('is_show_price')->default(0)->comment('Показывать цену')->after('is_new');
        });

        Schema::table('prices_services', function (Blueprint $table) {
            $table->boolean('is_discount')->default(1)->unsigned()->comment('Режим скидок')->after('price');

            $table->tinyInteger('discount_mode')->unsigned()->default(1)->comment('Тип скидки: 1 - проценты, 2 - валюта')->after('is_discount');
            $table->decimal('discount_percent', 10,2)->default(0)->comment('Процент скидки')->after('discount_mode');
            $table->decimal('discount_currency', 10,2)->default(0)->comment('Сумма скидки')->after('discount_percent');

            $table->bigInteger('price_discount_id')->nullable()->unsigned()->comment('Id скидки прайса')->after('discount_currency');
//            $table->foreign('price_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('price_discount', 10, 2)->default(0)->comment('Скидка по прайсу')->after('price_discount_id');
            $table->decimal('total_price_discount', 10, 2)->default(0)->comment('Сумма с скидкой по прайсу')->after('price_discount');

            $table->bigInteger('catalogs_item_discount_id')->nullable()->unsigned()->comment('Id скидки раздела каталога')->after('total_price_discount');
//            $table->foreign('catalogs_item_discount_id')->references('id')->on('discounts')->after('discount_currency');
            $table->decimal('catalogs_item_discount', 10, 2)->default(0)->comment('Скидкка по разделу каталога')->after('catalogs_item_discount_id');
            $table->decimal('total_catalogs_item_discount', 10, 2)->default(0)->comment('Сумма с скидкой по разделу каталога')->after('catalogs_item_discount');

            $table->bigInteger('estimate_discount_id')->nullable()->unsigned()->comment('Id скидки сметы')->after('total_catalogs_item_discount');
//            $table->foreign('estimate_discount_id')->references('id')->on('discounts');
            $table->decimal('estimate_discount', 10, 2)->default(0)->comment('Скидкка по смете')->after('estimate_discount_id');
            $table->decimal('total_estimate_discount', 10, 2)->default(0)->comment('Сумма с скидкой по смете')->after('estimate_discount');

            $table->decimal('total', 12,2)->default(0)->comment('Итоговая сумма')->after('total_estimate_discount');

            $table->boolean('is_show_price')->default(0)->comment('Показывать цену')->after('is_new');
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
                'is_vip_abc',
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
                'rfm',
                'abc',
                'xyz',
                'abcxyz',
                'activity',
                'discount',
                'discount_dynamic',
                'points',
            ]);
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn([
                'cost',
                'margin_percent',
                'margin_currency',
                'is_registered',
                'is_dismissed',
                'is_main',
                'external',
                'losses_from_points',
                'points',
                'surplus',
                'is_create_parse',
                'certificate_amount',
                'saled_date',
                'total_points',
                'total_bonuses'
            ]);
        });

        Schema::table('estimates_goods_items', function (Blueprint $table) {
            $table->dropColumn([
                'points',
                'price_discount_id',
                'price_discount',
                'total_price_discount',
                'catalogs_item_discount_id',
                'catalogs_item_discount',
                'total_catalogs_item_discount',
                'estimate_discount_id',
                'estimate_discount',
                'total_estimate_discount',
                'comment',
                'sale_mode',
                'total_points',
                'total_bonuses'
            ]);
        });

        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->dropColumn([
                'points',
                'price_discount_id',
                'price_discount',
                'total_price_discount',
                'catalogs_item_discount_id',
                'catalogs_item_discount',
                'total_catalogs_item_discount',
                'estimate_discount_id',
                'estimate_discount',
                'total_estimate_discount',
                'comment',
                'sale_mode',
                'total_points',
                'total_bonuses'
            ]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('foundation_date');
            $table->date('birthday_company')->nullable()->comment('Дата рождения компании')->after('seo_description');
        });

        Schema::table('widgets', function (Blueprint $table) {
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

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn([
                'mode',
                'horizontal',
                'vertical',
                'square',
                'prom',
            ]);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'is_create_parse',
                'is_link_parse',
                'order_amount_base',
                'need_delivery',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'login_date',
                'external',
            ]);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'video',
            ]);
        });

        Schema::table('catalogs_goods_items', function (Blueprint $table) {
            $table->dropColumn([
                'header',
                'is_discount',
                'is_hide_submenu',
            ]);
        });

        Schema::table('catalogs_services_items', function (Blueprint $table) {
            $table->dropColumn([
                'header',
                'is_discount',
                'is_hide_submenu',
            ]);
        });

        Schema::table('prices_goods', function (Blueprint $table) {
            $table->dropColumn([
                'is_discount',
                'discount_mode',
                'discount_percent',
                'discount_currency',
                'price_discount_id',
                'price_discount',
                'total_price_discount',
                'catalogs_item_discount_id',
                'catalogs_item_discount',
                'total_catalogs_item_discount',
                'estimate_discount_id',
                'estimate_discount',
                'total_estimate_discount',
                'total',
                'is_show_price',
            ]);
        });

        Schema::table('prices_services', function (Blueprint $table) {
            $table->dropColumn([
                'is_discount',
                'discount_mode',
                'discount_percent',
                'discount_currency',
                'price_discount_id',
                'price_discount',
                'total_price_discount',
                'catalogs_item_discount_id',
                'catalogs_item_discount',
                'total_catalogs_item_discount',
                'estimate_discount_id',
                'estimate_discount',
                'total_estimate_discount',
                'total',
                'is_show_price',
            ]);
        });
    }
}
