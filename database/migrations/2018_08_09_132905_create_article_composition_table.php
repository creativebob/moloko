<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCompositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_composition', function (Blueprint $table) {

            $table->integer('article_id')->nullable()->unsigned()->comment('Id артикула');
            $table->foreign('article_id')->references('id')->on('articles');

            // $table->morphs('composition');

            $table->integer('raw_id')->nullable()->unsigned()->comment('Id сырья');
            $table->foreign('raw_id')->references('id')->on('raws');

            $table->integer('value')->nullable()->unsigned()->comment('Значение');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_composition');
    }
}
