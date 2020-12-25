<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkplaceStafferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workplace_staffer', function (Blueprint $table) {
            $table->bigInteger('workplace_id')->nullable()->unsigned()->comment('Id рабочего места');
//            $table->foreign('workplace_id')->references('id')->on('workplaces');

            $table->bigInteger('staffer_id')->nullable()->unsigned()->comment('Id ставки');
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
        Schema::dropIfExists('workplace_staffer');
    }
}
