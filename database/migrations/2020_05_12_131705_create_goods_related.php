<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsRelated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_related', function (Blueprint $table) {
            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id товара');
//            $table->foreign('goods_id')->references('id')->on('goods');

            $table->bigInteger('related_id')->nullable()->unsigned()->comment('Id сопуствующего товара');
//            $table->foreign('related_id')->references('id')->on('goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_related');
    }
}
