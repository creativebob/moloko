<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstimateLabelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimate_label', function (Blueprint $table) {
            $table->bigInteger('estimate_id')->nullable()->unsigned()->comment('Id сметы');
//            $table->foreign('estimate_id')->references('id')->on('estimates');

            $table->bigInteger('label_id')->nullable()->unsigned()->comment('Id метки заказа');
//            $table->foreign('label_id')->references('id')->on('labels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estimate_label');
    }
}
