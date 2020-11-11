<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletTaxationTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_taxation_type', function (Blueprint $table) {
            $table->bigInteger('outlet_id')->nullable()->unsigned()->comment('Id торговой точки');
//            $table->foreign('outlet_id')->references('id')->on('outlets');

            $table->bigInteger('taxation_type_id')->nullable()->unsigned()->comment('Id системы налогообложения');
//            $table->foreign('taxation_type_id')->references('id')->on('taxation_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outlet_taxation_type');
    }
}
