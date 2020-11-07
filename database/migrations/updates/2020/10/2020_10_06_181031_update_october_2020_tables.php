<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOctober2020Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalogs_goods', function (Blueprint $table) {
            $table->boolean('is_access_page')->default(1)->comment('Страница товара')->after('slug');
            $table->boolean('is_check_stock')->default(0)->comment('Наличие на складе')->after('is_access_page');
        });

        Schema::table('catalogs_services', function (Blueprint $table) {
            $table->boolean('is_access_page')->default(1)->comment('Страница товара')->after('slug');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->boolean('is_autochange')->default(0)->comment('Авто-смена слайдов')->after('is_slider');
            $table->integer('delay')->nullable()->comment('Время задержки')->after('is_autochange');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payments_type_id',
                'amount',
                'date',
            ]);

            $table->timestamp('registered_at')->nullable()->comment('Время регистрации')->after('document_id');

            $table->decimal('cash', 10, 2)->default(0)->comment('Сумма наличного платежа')->after('registered_at');
            $table->decimal('electronically', 10, 2)->default(0)->comment('Сумма электронного платежа')->after('cash');

            $table->string('type')->comment('Тип')->after('electronically');

            $table->decimal('total', 10, 2)->default(0)->comment('Итого оплачено')->after('type');

            $table->decimal('change', 10, 2)->default(0)->comment('Сдача')->after('total');

            $table->bigInteger('payments_method_id')->unsigned()->nullable()->comment('Id метода платежа')->after('change');
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropForeign('dispatches_channel_id_foreign');

            $table->dropColumn([
                'name',
                'body',
                'channel_id'
            ]);

            $table->morphs('entity');

            $table->string('email')->comment('Email')->after('entity_id');

            $table->boolean('is_delivered')->default(0)->comment('Доставлено')->after('email');
            $table->boolean('is_opened')->default(0)->comment('Открыто')->after('is_delivered');
            $table->boolean('is_spamed')->default(0)->comment('Спам')->after('is_opened');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('utm_source')->nullable()->comment('Источник')->after('campaign_id');
            $table->string('utm_campaign')->nullable()->comment('Рекламаня кампания')->after('utm_source');
            $table->string('utm_medium')->nullable()->comment('Тип')->after('utm_campaign');

            $table->string('prom')->nullable()->comment('Продвижение')->after('utm_term');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->bigInteger('taxation_type_id')->nullable()->unsigned()->comment('Тип системы налогообложения')->after('foundation_date');
//            $table->foreign('taxation_type_id')->references('id')->on('taxation_types');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->morphs('storage');
        });

        Schema::table('offs', function (Blueprint $table) {
            $table->morphs('storage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalogs_goods', function (Blueprint $table) {
            $table->dropColumn([
                'is_access_page',
                'is_check_stock',
            ]);
        });

        Schema::table('catalogs_services', function (Blueprint $table) {
            $table->dropColumn([
                'is_access_page',
            ]);
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn([
                'is_autochange',
                'delay',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'registered_at',
                'cash',
                'electronically',
                'type',
                'total',
                'change',
                'payments_method_id',
            ]);

            $table->bigInteger('payments_type_id')->unsigned()->nullable()->comment('Id типа платежа');
            $table->decimal('amount', 12, 4)->default(0)->comment('Сумма');
            $table->date('date')->nullable()->comment('Дата');
        });

        Schema::table('dispatches', function (Blueprint $table) {
            $table->dropColumn([
                'entity_type',
                'entity_id',
                'email',
                'is_delivered',
                'is_opened',
                'is_spamed',
            ]);

            $table->string('name')->nullable()->comment('Название');
            $table->text('body')->nullable()->comment('Текст');

            $table->bigInteger('channel_id')->unsigned()->nullable()->comment('Id канала');
//            $table->foreign('channel_id')->references('id')->on('channels');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'utm_source',
                'utm_campaign',
                'utm_medium',
                'prom',
            ]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'taxation_type_id',
            ]);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn([
                'storage_id',
                'storage_type',
            ]);
        });

        Schema::table('offs', function (Blueprint $table) {
            $table->dropColumn([
                'storage_id',
                'storage_type',
            ]);
        });
    }
}
