<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('triggers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index()->comment('Название');
            $table->string('alias')->index()->comment('Алиас');
            $table->text('description')->nullable()->comment('Описание');

            $table->bigInteger('entity_id')->unsigned()->nullable()->comment('Id сущности');
            $table->foreign('entity_id')->references('id')->on('entities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('triggers');
    }
}
