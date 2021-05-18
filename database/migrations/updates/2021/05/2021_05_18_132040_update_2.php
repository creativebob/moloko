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
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('ID аватара')->after('call_date');
        });

        Schema::table('services_flows', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable()->comment('Время отмены')->after('location_id');
        });

        Schema::table('events_flows', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable()->comment('Время отмены')->after('location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropColumn('photo_id');
        });

        Schema::table('services_flows', function (Blueprint $table) {
            $table->dropColumn('canceled_at');
        });

        Schema::table('events_flows', function (Blueprint $table) {
            $table->dropColumn('canceled_at');
        });
    }
}
