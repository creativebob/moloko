<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settingable', function (Blueprint $table) {

            $table->morphs('entity');

            $table->bigInteger('setting_id')->nullable()->unsigned()->comment('Id настройки');
            $table->foreign('setting_id')->references('id')->on('settings');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settingable');
    }
}
