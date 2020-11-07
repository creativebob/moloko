<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletCatalogServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_catalog_services', function (Blueprint $table) {
            $table->bigInteger('outlet_id')->nullable()->unsigned()->comment('Id торговой точки');
//            $table->foreign('outlet_id')->references('id')->on('outlets');

            $table->bigInteger('catalog_services_id')->nullable()->unsigned()->comment('Id каталога услуг');
//            $table->foreign('catalog_services_id')->references('id')->on('catalogs_services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outlet_catalog_services');
    }
}
