<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkplaceToolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workplace_tool', function (Blueprint $table) {
            $table->bigInteger('workplace_id')->nullable()->unsigned()->comment('Id рабочего места');
//            $table->foreign('workplace_id')->references('id')->on('workplaces');

            $table->bigInteger('tool_id')->nullable()->unsigned()->comment('Id инструмента');
//            $table->foreign('otool_id')->references('id')->on('tools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workplace_tool');
    }
}
