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
        Schema::table('catalogs_services_items', function (Blueprint $table) {
            $table->bigInteger('seo_id')->nullable()->unsigned()->comment('Id seo')->after('level');
            $table->foreign('seo_id')->references('id')->on('seos');
        });

        Schema::table('catalogs_goods_items', function (Blueprint $table) {
            $table->bigInteger('seo_id')->nullable()->unsigned()->comment('Id seo')->after('level');
            $table->foreign('seo_id')->references('id')->on('seos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalogs_services_items', function (Blueprint $table) {
            $table->dropForeign(['seo_id']);
            $table->dropColumn('seo_id');
        });

        Schema::table('catalogs_goods_items', function (Blueprint $table) {
            $table->dropForeign(['seo_id']);
            $table->dropColumn('seo_id');
        });
    }
}
