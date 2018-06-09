<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetricEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metric_entity', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('metric_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('metric_id')->references('id')->on('metrics');
            
            $table->integer('entity_id')->nullable()->unsigned()->comment('Id сущности связанной с метрикой');
            $table->string('entity')->index()->comment('Сущность обьекта');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metric_entity');
    }
}
