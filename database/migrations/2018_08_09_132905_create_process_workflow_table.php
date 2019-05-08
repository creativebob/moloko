<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_workflow', function (Blueprint $table) {

            $table->integer('process_id')->nullable()->unsigned()->comment('Id процесса');
            $table->foreign('process_id')->references('id')->on('processes');

            // $table->morphs('composition');

            $table->integer('workflow_id')->nullable()->unsigned()->comment('Id рабочего процесса');
            $table->foreign('workflow_id')->references('id')->on('workflows');

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
        Schema::dropIfExists('process_workflow');
    }
}
