<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_goods', function (Blueprint $table) {
            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id товара');
            $table->foreign('goods_id')->references('id')->on('goods');

            $table->integer('count')->comment('Количество');

            $table->decimal('weight', 15, 2)->nullable()->comment('Вес (кг)');

            $table->string('serial')->comment('Серийный номер');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_goods');
    }
}
