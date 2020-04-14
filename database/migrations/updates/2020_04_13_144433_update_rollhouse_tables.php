<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRollhouseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('video_url')->nullable()->comment('Url видео')->after('content');
            $table->text('video')->nullable()->comment('Блок видео')->after('video_url');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->text('seo_description')->nullable()->comment('Описание для сайта')->after('description');
            $table->text('content')->nullable()->comment('Контент')->after('seo_description');
            $table->text('keywords')->nullable()->comment('Ключевики')->after('content');
        });
        Schema::table('processes', function (Blueprint $table) {
            $table->text('seo_description')->nullable()->comment('Описание для сайта')->after('description');
            $table->text('content')->nullable()->comment('Контент')->after('seo_description');
            $table->text('keywords')->nullable()->comment('Ключевики')->after('content');

            $table->bigInteger('unit_length_id')->nullable()->unsigned()->comment('Id единицы измерения продолжительности')->after('unit_id');
            $table->foreign('unit_length_id')->references('id')->on('units');
        });

        Schema::table('consignments_items', function (Blueprint $table) {
            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты')->after('amount');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
        Schema::table('prices_goods_histories', function (Blueprint $table) {
            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты')->after('price');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
        Schema::table('prices_services_histories', function (Blueprint $table) {
            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты')->after('price');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropForeign('estimates_stock_id_foreign');
            $table->dropColumn('stock_id');

            $table->boolean('is_registered')->default(0)->comment('Зарегистрирована')->after('draft');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign('settings_user_id_foreign');
            $table->dropColumn('user_id');

            $table->dropColumn('key');
            $table->dropColumn('value');

            $table->dropForeign('settings_company_id_foreign');
            $table->dropColumn('company_id');

            $table->dropColumn('sort');
            $table->dropColumn('display');
            $table->dropColumn('system');
            $table->dropColumn('moderation');

            $table->dropForeign('settings_author_id_foreign');
            $table->dropColumn('author_id');

            $table->dropColumn('editor_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('deleted_at');

            $table->string('name')->comment('Название');
            $table->string('alias')->nullable()->comment('Алиас');
        });

        Schema::table('goods', function (Blueprint $table) {
            $table->boolean('is_produced')->default(0)->comment('Производится')->after('serial');
            $table->boolean('is_ordered')->default(0)->comment('Заказывается')->after('is_produced');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->text('description')->nullable()->comment('Описание')->after('name');
            $table->boolean('archive')->default(0)->comment('Архив')->after('sector_id');

            $table->dropColumn('deleted_at');
        });
        Schema::table('staff', function (Blueprint $table) {
            $table->boolean('archive')->default(0)->comment('Архив')->after('rate');

            $table->dropColumn('deleted_at');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });

        Schema::table('estimates_goods_items', function (Blueprint $table) {
            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты')->after('amount');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->bigInteger('currency_id')->nullable()->unsigned()->comment('Id валюты')->after('amount');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });

        // TODO - 13.04.20 - На РХ это поле есть
//        Schema::table('cities', function (Blueprint $table) {
//            $table->string('prepositional_case')->nullable()->comment('Название в предложном падеже');
//        });

        // TODO - 13.04.20 - Связь можно создать только с нововставленными таблицами, либо если вставляется поле, т оего можно привязать к уже существующим
//        Schema::table('company_currency', function (Blueprint $table) {
//            $table->foreign('company_id')->references('id')->on('companies');
//            $table->foreign('currency_id')->references('id')->on('currencies');
//        });

//        Schema::table('vendors', function (Blueprint $table) {
//            $table->foreign('supplier_id')->references('id')->on('suppliers');
//            $table->foreign('company_id')->references('id')->on('companies');
//            $table->foreign('author_id')->references('id')->on('users');
//        });
//        Schema::table('process_position', function (Blueprint $table) {
//            $table->foreign('process_id')->references('id')->on('processes');
//            $table->foreign('position_id')->references('id')->on('positions');
//        });
//        Schema::table('settingable', function (Blueprint $table) {
//            $table->foreign('setting_id')->references('id')->on('settings');
//        });
//        Schema::table('portfolios', function (Blueprint $table) {
//            $table->string('alias')->nullable()->comment('Алиас');
//            $table->string('slug')->nullable()->comment('Слаг');
//            $table->text('seo_description')->nullable()->comment('Описание для сайта');

//            $table->foreign('photo_id')->references('id')->on('photos');
//            $table->foreign('company_id')->references('id')->on('companies');
//            $table->foreign('author_id')->references('id')->on('users');
//        });
        Schema::table('portfolios_items', function (Blueprint $table) {
            $table->foreign('portfolio_id')->references('id')->on('portfolios');
//            $table->foreign('photo_id')->references('id')->on('photos');
            $table->foreign('parent_id')->references('id')->on('portfolios_items');
            $table->foreign('category_id')->references('id')->on('portfolios_items');
//            $table->foreign('display_mode_id')->references('id')->on('display_modes');
//            $table->foreign('directive_category_id')->references('id')->on('units_categories');
//            $table->foreign('company_id')->references('id')->on('companies');
//            $table->foreign('author_id')->references('id')->on('users');
        });
        Schema::table('business_cases', function (Blueprint $table) {
            $table->foreign('portfolios_item_id')->references('id')->on('portfolios');
//            $table->foreign('photo_id')->references('id')->on('photos');
//            $table->foreign('company_id')->references('id')->on('companies');
//            $table->foreign('author_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {

            $table->dropColumn('video_url');
            $table->dropColumn('video');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('seo_description');
            $table->dropColumn('content');
            $table->dropColumn('keywords');
        });
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('seo_description');
            $table->dropColumn('content');
            $table->dropColumn('keywords');

            $table->dropForeign('processes_unit_length_id_foreign');
            $table->dropColumn('unit_length_id');
        });

        Schema::table('consignments_items', function (Blueprint $table) {
            $table->dropForeign('consignments_items_currency_id_foreign');
            $table->dropColumn('currency_id');
        });
        Schema::table('prices_goods_histories', function (Blueprint $table) {
            $table->dropForeign('prices_goods_histories_currency_id_foreign');
            $table->dropColumn('currency_id');
        });
        Schema::table('prices_services_histories', function (Blueprint $table) {
            $table->dropForeign('prices_services_histories_currency_id_foreign');
            $table->dropColumn('currency_id');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->dropColumn('is_registered');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->unsigned()->comment('Id пользователя');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('key')->nullable()->comment('Ключ настройки');
            $table->string('value')->nullable()->comment('Значение настройки');

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

        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('is_produced');
            $table->dropColumn('is_ordered');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('archive');
            $table->softDeletes();
        });
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('archive');
            $table->softDeletes();
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('estimates_goods_items', function (Blueprint $table) {
            $table->dropForeign('estimates_goods_items_currency_id_foreign');
            $table->dropColumn('currency_id');
        });
        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->dropForeign('estimates_services_items_currency_id_foreign');
            $table->dropColumn('currency_id');
        });

//        Schema::table('cities', function (Blueprint $table) {
//            $table->dropColumn('prepositional_case');
//        });

//        Schema::table('company_currency', function (Blueprint $table) {
//            $table->dropForeign('company_currency_company_id_foreign');
//            $table->dropForeign('company_currency_currency_id_foreign');
//        });

//        Schema::table('vendors', function (Blueprint $table) {
//            $table->dropForeign('vendors_supplier_id_foreign');
//            $table->dropForeign('vendors_company_id_foreign');
//            $table->dropForeign('vendors_author_id_foreign');
//        });
//        Schema::table('process_position', function (Blueprint $table) {
//            $table->dropForeign('process_position_process_id_foreign');
//            $table->dropForeign('process_position_position_id_foreign');
//        });
//        Schema::table('settingable', function (Blueprint $table) {
//            $table->dropForeign('settingable_setting_id_foreign');
//        });
//        Schema::table('portfolios', function (Blueprint $table) {
//            $table->dropColumn('alias');
//            $table->dropColumn('slug');
//            $table->dropColumn('seo_description');

//            $table->dropForeign('portfolios_photo_id_foreign');
//            $table->dropForeign('portfolios_company_id_foreign');
//            $table->dropForeign('portfolios_author_id_foreign');
//        });
        Schema::table('portfolios_items', function (Blueprint $table) {
            $table->dropForeign('portfolios_items_portfolio_id_foreign');
//            $table->dropForeign('portfolios_items_photo_id_foreign');
            $table->dropForeign('portfolios_items_parent_id_foreign');
            $table->dropForeign('portfolios_items_category_id_foreign');
//            $table->dropForeign('portfolios_items_display_mode_id_foreign');
//            $table->dropForeign('portfolios_items_directive_category_id_foreign');
//            $table->dropForeign('portfolios_items_company_id_foreign');
//            $table->dropForeign('portfolios_items_author_id_foreign');
        });
        Schema::table('business_cases', function (Blueprint $table) {
            $table->dropForeign('business_cases_portfolios_item_id_foreign');
//            $table->dropForeign('business_cases_photo_id_foreign');
//            $table->dropForeign('business_cases_company_id_foreign');
//            $table->dropForeign('business_cases_author_id_foreign');
        });
    }
}
