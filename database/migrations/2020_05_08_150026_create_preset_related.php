<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresetRelated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preset_related', function (Blueprint $table) {
            $table->bigInteger('goods_category_id')->nullable()->unsigned()->comment('Id категории');
//            $table->foreign('goods_category_id')->references('id')->on('goods_categories');

            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id сопуствующего товара');
//            $table->foreign('goods_id')->references('id')->on('goods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preset_related');
    }
}
