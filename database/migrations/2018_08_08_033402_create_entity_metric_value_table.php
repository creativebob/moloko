<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityMetricValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_metric_value', function (Blueprint $table) {
            $table->morphs('entity');

            $table->bigInteger('metric_id')->nullable()->unsigned()->comment('Id метрики');
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
        Schema::dropIfExists('entity_metric_value');
    }
}
