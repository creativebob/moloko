<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsServiceFilialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_service_filial', function (Blueprint $table) {

            $table->bigInteger('catalogs_service_id')->nullable()->unsigned()->comment('Id каталога услуг');
            $table->foreign('catalogs_service_id')->references('id')->on('catalogs_services');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs_service_filial');
    }
}
