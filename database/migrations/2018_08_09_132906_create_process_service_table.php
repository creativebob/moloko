<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_service', function (Blueprint $table) {

            $table->bigInteger('process_id')->nullable()->unsigned()->comment('Id процесса');
            $table->foreign('process_id')->references('id')->on('processes');

            // $table->morphs('composition');

            $table->bigInteger('service_id')->nullable()->unsigned()->comment('Id услуги');
            $table->foreign('service_id')->references('id')->on('services');

            $table->integer('value')->nullable()->unsigned()->comment('Значение');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_service');
    }
}
