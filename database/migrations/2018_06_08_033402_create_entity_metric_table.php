<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityMetricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_metric', function (Blueprint $table) {
            $table->bigInteger('metric_id')->nullable()->unsigned()->comment('Id метрики');
            $table->foreign('metric_id')->references('id')->on('metrics');

            $table->bigInteger('entity_id')->nullable()->unsigned()->comment('Id сущности');
            $table->foreign('entity_id')->references('id')->on('entities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_metric');
    }
}
