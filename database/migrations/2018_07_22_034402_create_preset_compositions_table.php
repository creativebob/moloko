<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresetCompositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preset_compositions', function (Blueprint $table) {

            // $table->integer('category_id')->nullable()->unsigned()->comment('Id категории');
            // $table->foreign('category_id')->references('id')->on('goods_categories');
            $table->morphs('preset_composition', 'pres_comp');

            $table->integer('article_id')->nullable()->unsigned()->comment('Id состава');
            $table->foreign('article_id')->references('id')->on('articles');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preset_compositions');
    }
}
