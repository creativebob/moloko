<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('choices_id')->nullable()->unsigned()->comment('Id сущности');
            $table->string('choices_type')->nullable()->comment('Сущность');

            $table->integer('lead_id')->nullable()->unsigned()->comment('Id лида');
            $table->foreign('lead_id')->references('id')->on('leads');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('choices');
    }
}
