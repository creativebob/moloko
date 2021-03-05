<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImpactMetricTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impact_metric', function (Blueprint $table) {
            $table->bigInteger('impact_id')->nullable()->unsigned()->comment('Id объекта воздействия');
//            $table->foreign('impact_id')->references('id')->on('impacts');

            $table->bigInteger('metric_id')->nullable()->unsigned()->comment('Id метрики');
//            $table->foreign('metric_id')->references('id')->on('metrics');

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
        Schema::dropIfExists('impact_metric');
    }
}
