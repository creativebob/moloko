<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientServicesFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_services_flow', function (Blueprint $table) {
            $table->bigInteger('client_id')->nullable()->unsigned()->comment('Id клиента');
//            $table->foreign('client_id')->references('id')->on('clients');

            $table->bigInteger('services_flow_id')->nullable()->unsigned()->comment('Id потока услуг');
//            $table->foreign('services_flow_id')->references('id')->on('services_flows');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_services_flow');
    }
}
