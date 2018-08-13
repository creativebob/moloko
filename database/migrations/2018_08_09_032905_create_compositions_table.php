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

            $table->integer('goods_category_id')->nullable()->unsigned()->comment('Id категории продукции');
            $table->foreign('goods_category_id')->references('id')->on('goods_categories');
            
            $table->integer('compositions_id')->nullable()->unsigned()->comment('Id сущности связанной с категорией');

            $table->string('compositions_type')->nullable()->comment('Имя сущности');

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
