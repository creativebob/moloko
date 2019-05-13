<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresetWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preset_workflow', function (Blueprint $table) {

            $table->bigInteger('services_category_id')->nullable()->unsigned()->comment('Id категории');
            $table->foreign('services_category_id')->references('id')->on('services_categories');
            // $table->morphs('preset_composition', 'pres_comp');

            $table->bigInteger('workflow_id')->nullable()->unsigned()->comment('Id рабочего процесса');
            $table->foreign('workflow_id')->references('id')->on('workflows');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preset_workflow');
    }
}
