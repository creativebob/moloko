<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsMetricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_metric', function (Blueprint $table) {

            $table->integer('goods_id')->nullable()->unsigned()->comment('Id товара');
            $table->foreign('goods_id')->references('id')->on('goods');

            $table->integer('metric_id')->nullable()->unsigned()->comment('Id метрики');
            $table->foreign('metric_id')->references('id')->on('metrics');

            $table->string('value')->nullable()->comment('Значение');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_metric');
    }
}
