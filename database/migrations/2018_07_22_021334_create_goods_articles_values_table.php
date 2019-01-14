<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsArticlesValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_articles_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('goods_article_id')->nullable()->unsigned()->comment('ID метрики');
            $table->foreign('goods_article_id')->references('id')->on('goods_articles');

            $table->morphs('goods_articles_values');

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
        Schema::dropIfExists('goods_articles_values');
    }
}
