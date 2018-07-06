<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compositions', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('products_category_id')->nullable()->unsigned()->comment('Id категории продукции');
            $table->foreign('products_category_id')->references('id')->on('products_categories');
            
            $table->integer('composition_id')->nullable()->unsigned()->comment('Id сущности связанной с категорией');
            $table->foreign('composition_id')->references('id')->on('products');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compositions');
    }
}
