<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsFlowStafferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_flow_staffer', function (Blueprint $table) {
            $table->bigInteger('events_flow_id')->nullable()->unsigned()->comment('Id потока событий');
//            $table->foreign('events_flow_id')->references('id')->on('events_flows');

            $table->bigInteger('staffer_id')->nullable()->unsigned()->comment('Id штата');
//            $table->foreign('staffer_id')->references('id')->on('staff');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_flow_staffer');
    }
}
