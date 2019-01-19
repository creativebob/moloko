<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManufacturerSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manufacturer_supplier', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('manufacturer_id')->unsigned()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

            $table->integer('supplier_id')->unsigned()->comment('Id поставщика');
            $table->foreign('supplier_id')->references('id')->on('suppliers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufacturer_supplier');
    }
}
