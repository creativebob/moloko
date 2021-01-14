<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessImpactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_impact', function (Blueprint $table) {
            $table->bigInteger('process_id')->nullable()->unsigned()->comment('Id процесса');
//            $table->foreign('process_id')->references('id')->on('processes');

            $table->bigInteger('impact_id')->nullable()->unsigned()->comment('Id обьекта воздействия');
//            $table->foreign('impact_id')->references('id')->on('impacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_impact');
    }
}
