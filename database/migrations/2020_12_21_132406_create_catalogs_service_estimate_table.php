<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogsServiceEstimateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_service_estimate', function (Blueprint $table) {
            $table->bigInteger('catalogs_service_id')->nullable()->unsigned()->comment('Id каталога услуг');
//            $table->foreign('catalogs_service_id')->references('id')->on('catalogs_services');

            $table->bigInteger('estimate_id')->nullable()->unsigned()->comment('Id сметы');
//            $table->foreign('estimate_id')->references('id')->on('estimates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs_service_estimate');
    }
}
