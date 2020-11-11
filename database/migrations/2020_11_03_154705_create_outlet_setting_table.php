<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_setting', function (Blueprint $table) {
            $table->bigInteger('outlet_id')->nullable()->unsigned()->comment('Id торговой точки');
//            $table->foreign('outlet_id')->references('id')->on('outlets');

            $table->bigInteger('setting_id')->nullable()->unsigned()->comment('Id настройки');
//            $table->foreign('setting_id')->references('id')->on('outlets_settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outlet_setting');
    }
}
