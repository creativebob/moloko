<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectionCompetitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direction_competitor', function (Blueprint $table) {
            $table->bigInteger('direction_id')->nullable()->unsigned()->index()->comment('Id направления');
            $table->foreign('direction_id')->references('id')->on('directions');

            $table->bigInteger('competitor_id')->nullable()->unsigned()->index()->comment('Id конкурента');
            $table->foreign('competitor_id')->references('id')->on('competitors');           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direction_competitor');
    }
}
