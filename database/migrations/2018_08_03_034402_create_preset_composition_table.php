<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresetCompositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preset_composition', function (Blueprint $table) {

            $table->integer('goods_category_id')->nullable()->unsigned()->comment('Id категории');
            $table->foreign('goods_category_id')->references('id')->on('goods_categories');
            // $table->morphs('preset_composition', 'pres_comp');

            $table->integer('raw_id')->nullable()->unsigned()->comment('Id сырья');
            $table->foreign('raw_id')->references('id')->on('raws');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preset_composition');
    }
}
