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
        Schema::table('processes', function (Blueprint $table) {
            $table->boolean('is_auto_initiated')->default(1)->comment('Автоинициация')->after('unit_length_id');
        });

        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->bigInteger('flow_id')->unsigned()->nullable()->comment('Id потока')->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('is_auto_initiated');
        });

        Schema::table('estimates_services_items', function (Blueprint $table) {
            $table->dropColumn('flow_id');
        });
    }
}
