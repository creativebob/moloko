<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesGroupEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles_group_entity', function (Blueprint $table) {
            $table->bigInteger('articles_group_id')->nullable()->unsigned()->comment('Id группы');
            $table->foreign('articles_group_id')->references('id')->on('articles_groups');

            $table->morphs('entity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles_group_entity');
    }
}
