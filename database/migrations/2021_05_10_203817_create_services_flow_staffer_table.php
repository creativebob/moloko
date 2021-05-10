<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesFlowStafferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_flow_staffer', function (Blueprint $table) {
            $table->bigInteger('services_flow_id')->nullable()->unsigned()->comment('Id потока событий');
//            $table->foreign('services_flow_id')->references('id')->on('services_flows');

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
        Schema::dropIfExists('services_flow_staffer');
    }
}
