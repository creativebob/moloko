<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountEstimateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_estimate', function (Blueprint $table) {
            $table->bigInteger('discount_id')->unsigned()->comment('Id скидки');
//            $table->foreign('discount_id')->references('id')->on('discounts');
            
            $table->bigInteger('estimate_id')->unsigned()->comment('Id сметы');
//            $table->foreign('estimate_id')->references('id')->on('estimates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_estimate');
    }
}
