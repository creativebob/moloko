<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetricEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metric_entities', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('metric_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('metric_id')->references('id')->on('metrics');

            $table->morphs('metric_entity');

            $table->enum('set_status', ['one', 'set'])->comment('Статус набора (Один/набор)');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metric_entities');
    }
}
