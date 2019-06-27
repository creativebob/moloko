<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilialSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filial_site', function (Blueprint $table) {
            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала/отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('site_id')->nullable()->unsigned()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filial_site');
    }
}
