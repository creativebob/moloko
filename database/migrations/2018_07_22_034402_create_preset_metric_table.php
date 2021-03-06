<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresetMetricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preset_metric', function (Blueprint $table) {
            $table->morphs('entity');

            $table->bigInteger('metric_id')->nullable()->unsigned()->comment('Id метрики');
            $table->foreign('metric_id')->references('id')->on('metrics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preset_metric');
    }
}