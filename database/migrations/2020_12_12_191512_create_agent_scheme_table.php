<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSchemeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_scheme', function (Blueprint $table) {
            $table->bigInteger('agent_id')->nullable()->unsigned()->comment('Id агента');
//            $table->foreign('agent_id')->references('id')->on('agents');

            $table->bigInteger('agency_scheme_id')->nullable()->unsigned()->comment('Id агентской схемы');
//            $table->foreign('agency_scheme_id')->references('id')->on('agency_schemes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_scheme');
    }
}
