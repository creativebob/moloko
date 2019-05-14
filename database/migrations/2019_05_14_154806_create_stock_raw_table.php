<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockRawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_raw', function (Blueprint $table) {
            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('raw_id')->nullable()->unsigned()->comment('Id сырья');
            $table->foreign('raw_id')->references('id')->on('raws');

            $table->integer('count')->comment('Количество');

            $table->decimal('weight', 15, 2)->nullable()->comment('Вес (кг)');

            $table->string('Serial')->comment('Серийный номер');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_raw');
    }
}
