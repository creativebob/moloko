<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletPaymentsMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_payments_method', function (Blueprint $table) {
            $table->bigInteger('outlet_id')->nullable()->unsigned()->comment('Id торговой точки');
//            $table->foreign('outlet_id')->references('id')->on('outlets');

            $table->bigInteger('payments_method_id')->nullable()->unsigned()->comment('Id метода платежа');
//            $table->foreign('payments_method_id')->references('id')->on('payments_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outlet_payments_method');
    }
}
