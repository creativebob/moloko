<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogsItemService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs_item_service', function (Blueprint $table) {
            $table->integer('catalogs_item_id')->nullable()->unsigned()->comment('Id пункта каталога');
            $table->foreign('catalogs_item_id')->references('id')->on('catalogs_items');

            $table->integer('service_id')->nullable()->unsigned()->comment('Id услуги');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs_item_service');
    }
}
