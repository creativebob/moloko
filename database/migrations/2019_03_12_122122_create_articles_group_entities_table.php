<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesGroupEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles_group_entities', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('articles_group_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('articles_group_id')->references('id')->on('articles_groups');

            $table->morphs('articles_group_entity', 'art_gr_entity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles_group_entities');
    }
}
