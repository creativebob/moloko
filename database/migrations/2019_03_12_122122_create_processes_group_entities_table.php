<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesGroupEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processes_group_entities', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('processes_group_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('processes_group_id')->references('id')->on('processes_groups');

            $table->morphs('processes_group_entity', 'pro_gr_entity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processes_group_entities');
    }
}
