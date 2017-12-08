<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('access_group_name')->index()->unique()->comment('Имя категории пользователей');
            $table->integer('category_right_id')->nullable()->unsigned()->comment('Категория пользователей');
            $table->foreign('category_right_id')->references('id')->on('category_rights');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_groups');
    }
}
