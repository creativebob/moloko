<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawsArticlesValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raws_articles_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('raws_article_id')->nullable()->unsigned()->comment('ID метрики');
            $table->foreign('raw_article_id')->references('id')->on('raws_articles');

            $table->morphs('raws_articles_values');

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
        Schema::dropIfExists('raws_articles_values');
    }
}
