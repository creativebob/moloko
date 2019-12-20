<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_site', function (Blueprint $table) {
            $table->bigInteger('promotion_id')->nullable()->unsigned()->comment('Id акции');
            $table->foreign('promotion_id')->references('id')->on('promotions');

            $table->bigInteger('site_id')->nullable()->unsigned()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');

//            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
//            $table->foreign('filial_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_site');
    }
}
