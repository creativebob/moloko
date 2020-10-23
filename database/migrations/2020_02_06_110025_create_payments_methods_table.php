<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_methods', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->tinyInteger('sign')->comment('Значение реквизита');
            $table->string('name')->index()->comment('Формат ПФ');
            $table->text('description')->nullable()->comment('Основание');
            $table->string('alias')->comment('Алиас');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments_methods');
    }
}
