<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_setting', function (Blueprint $table) {
            $table->bigInteger('company_id')->nullable()->unsigned()->comment('Id компании');
//            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('setting_id')->nullable()->unsigned()->comment('Id настройки');
//            $table->foreign('setting_id')->references('id')->on('settings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_setting');
    }
}
