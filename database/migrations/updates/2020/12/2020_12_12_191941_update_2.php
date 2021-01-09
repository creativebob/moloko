<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('estimates', function (Blueprint $table) {
            $table->bigInteger('agent_id')->unsigned()->nullable()->comment('Id агента')->after('total_bonuses');

            $table->bigInteger('agency_scheme_id')->unsigned()->nullable()->comment('Id агентской схемы')->after('agent_id');

            $table->decimal('share_currency', 10,2)->default(0)->comment('Сумма агентсокго вознаграждения')->after('agency_scheme_id');
            $table->decimal('principal_currency', 10,2)->default(0)->comment('Сумма компании')->after('share_currency');
        });

        Schema::table('estimates_goods_items', function (Blueprint $table) {
            $table->bigInteger('agent_id')->unsigned()->nullable()->comment('Id агента')->after('total_bonuses');

            $table->bigInteger('agency_scheme_id')->unsigned()->nullable()->comment('Id агентской схемы')->after('agent_id');

            $table->decimal('share_percent', 5,2)->default(0)->comment('Процент агентсокго вознаграждения')->after('agency_scheme_id');
            $table->decimal('share_currency', 10,2)->default(0)->comment('Сумма агентсокго вознаграждения')->after('share_percent');
            $table->decimal('principal_currency', 10,2)->default(0)->comment('Сумма компании')->after('share_currency');
        });

        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->bigInteger('agent_id')->unsigned()->nullable()->comment('Id агента')->after('total_bonuses');

            $table->bigInteger('agency_scheme_id')->unsigned()->nullable()->comment('Id агентской схемы')->after('agent_id');

            $table->decimal('share_percent', 5,2)->default(0)->comment('Процент агентсокго вознаграждения')->after('agency_scheme_id');
            $table->decimal('share_currency', 10,2)->default(0)->comment('Сумма агентсокго вознаграждения')->after('share_percent');
            $table->decimal('principal_currency', 10,2)->default(0)->comment('Сумма компании')->after('share_currency');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала')->after('id');
        });

//        Schema::table('outlets', function (Blueprint $table) {
//            $table->boolean('is_main')->default(0)->comment('Главная')->after('extra_time');
//            $table->timestamp('archived_at')->nullable()->comment('Архив')->after('is_main');
//        });

//        Schema::table('agents', function (Blueprint $table) {
//            $table->timestamp('archived_at')->nullable()->comment('Архив')->after('archive');
//            $table->dropColumn([
//                'archive',
//            ]);
//        });

        Schema::table('tools', function (Blueprint $table) {
            $table->bigInteger('tools_type_id')->nullable()->unsigned()->comment('Id типа оборудования')->after('price_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn([
                'agent_id',
                'agency_scheme_id',
                'share_currency',
                'principal_currency',
            ]);
        });

        Schema::table('estimates_goods_items', function (Blueprint $table) {
            $table->dropColumn([
                'agent_id',
                'agency_scheme_id',
                'share_percent',
                'share_currency',
                'principal_currency',
            ]);
        });

        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->dropColumn([
                'agent_id',
                'agency_scheme_id',
                'share_percent',
                'share_currency',
                'principal_currency',
            ]);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'filial_id',
            ]);
        });

//        Schema::table('outlets', function (Blueprint $table) {
//            $table->dropColumn([
//                'is_main',
//                'archived_at'
//            ]);
//        });

//        Schema::table('agents', function (Blueprint $table) {
//            $table->boolean('archive')->default(0)->comment('Статус архива')->after('archived_at');
//            $table->dropColumn([
//                'archived_at',
//            ]);
//        });

        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn([
                'tools_type_id',
            ]);
        });
    }
}
