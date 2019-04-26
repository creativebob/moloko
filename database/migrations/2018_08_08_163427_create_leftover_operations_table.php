<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeftoverOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leftover_operations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->comment('Имя операции над остатком');
            $table->text('description')->nullable()->comment('Описание операции над остатком');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leftover_operations');
    }
}
