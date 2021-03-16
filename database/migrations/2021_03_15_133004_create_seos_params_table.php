<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeosParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seos_params', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('seo_id')->unsigned()->nullable()->comment('Id SEO');
            $table->foreign('seo_id')
                ->references('id')
                ->on('seos')
                ->onDelete('cascade');

            $table->string('param')->nullable()->comment('Параметр');
            $table->string('value')->nullable()->comment('Значение');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seos_params');
    }
}
