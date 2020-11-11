<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxation_types', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('Название');
            $table->string('alias')->nullable()->comment('Алиас');
            $table->text('description')->nullable()->comment('Описание');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxation_types');
    }
}
