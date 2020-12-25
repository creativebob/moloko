<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletToolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_tool', function (Blueprint $table) {
            $table->bigInteger('outlet_id')->nullable()->unsigned()->comment('Id торговой точки');
//            $table->foreign('outlet_id')->references('id')->on('outlets');

            $table->bigInteger('tool_id')->nullable()->unsigned()->comment('Id инструмента');
//            $table->foreign('tool_id')->references('id')->on('tools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outlet_tool');
    }
}
