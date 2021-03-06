<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->bigInteger('estimate_id')->unsigned()->nullable()->comment('Id сметы')->after('stock_id');
        });

        Schema::table('productions_items', function (Blueprint $table) {
            $table->bigInteger('estimates_goods_item_id')->unsigned()->nullable()->comment('Id пункта сметы')->after('stock_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->bigInteger('legal_location_id')->nullable()->unsigned()->comment('Юридический адрес компании')->after('location_id');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn('produced_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('estimate_id');
        });

        Schema::table('productions_items', function (Blueprint $table) {
            $table->dropColumn('estimates_goods_item_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('legal_location_id');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->timestamp('produced_at')->nullable()->comment('Время производства')->after('registered_at');
        });
    }
}
