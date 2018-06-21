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

            $table->integer('product_id')->nullable()->unsigned()->comment('Id продукта');
            $table->foreign('product_id')->references('id')->on('products');
            
            $table->integer('composition_id')->nullable()->unsigned()->comment('Id сущности связанной с метрикой');
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
