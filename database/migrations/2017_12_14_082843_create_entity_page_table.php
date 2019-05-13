<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_page', function (Blueprint $table) {

            $table->bigInteger('entity_id')->nullable()->unsigned()->comment('ID сущности');
            $table->foreign('entity_id')->references('id')->on('entities');

            $table->bigInteger('page_id')->nullable()->unsigned()->comment('ID страницы сайта');
            $table->foreign('page_id')->references('id')->on('pages');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_page');
    }
}
