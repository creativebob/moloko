<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->bigInteger('seo_id')->nullable()->unsigned()->comment('Id seo')->after('photo_id');
            $table->foreign('seo_id')->references('id')->on('seos');
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->bigInteger('seo_id')->nullable()->unsigned()->comment('Id seo')->after('photo_id');
            $table->foreign('seo_id')->references('id')->on('seos');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->bigInteger('seo_id')->nullable()->unsigned()->comment('Id seo')->after('photo_id');
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
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['seo_id']);
            $table->dropColumn('seo_id');
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->dropForeign(['seo_id']);
            $table->dropColumn('seo_id');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['seo_id']);
            $table->dropColumn('seo_id');
        });
    }
}
