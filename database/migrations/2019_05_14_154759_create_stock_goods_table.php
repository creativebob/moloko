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

            $table->bigIncrements('id');
            
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id товара');
            $table->foreign('goods_id')->references('id')->on('goods');

            $table->integer('count')->default(0)->comment('Количество');
            $table->decimal('weight', 15, 2)->nullable()->comment('Вес (кг)');
            $table->string('serial')->nullable()->comment('Серийный номер');

            $table->bigInteger('manufacturer_id')->nullable()->unsigned()->comment('ID контрагента');
            $table->foreign('manufacturer_id')->references('id')->on('companies');

            $table->timestamps();
            $table->softDeletes();
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
