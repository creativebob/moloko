<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRubricatorSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rubricator_site', function (Blueprint $table) {
            $table->bigInteger('rubricator_id')->nullable()->unsigned()->comment('Id рубрики');
            $table->foreign('rubricator_id')->references('id')->on('rubricators');

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
        Schema::dropIfExists('rubricator_site');
    }
}
