<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_manufacturers', function (Blueprint $table) {
//            $table->bigIncrements('id');

            $table->bigInteger('manufacturer_id')->unsigned()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

            $table->morphs('categories_manufacturer', 'cat_manuf');

            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories_manufacturers');
    }
}
