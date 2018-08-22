<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_products', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('catalog_id')->nullable()->unsigned()->comment('Id каталога');
            $table->foreign('catalog_id')->references('id')->on('catalogs');
            
            $table->integer('catalog_products_id')->nullable()->unsigned()->comment('Id сущности связанной с каталогом');
            $table->string('catalog_products_type')->comment('Сущность обьекта');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

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
        Schema::dropIfExists('catalog_products');
    }
}
