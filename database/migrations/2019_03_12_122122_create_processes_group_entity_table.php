<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesGroupEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processes_group_entity', function (Blueprint $table) {
            $table->bigInteger('processes_group_id')->nullable()->unsigned()->comment('Id группы');
            $table->foreign('processes_group_id')->references('id')->on('processes_groups');

            $table->morphs('entity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processes_group_entity');
    }
}
