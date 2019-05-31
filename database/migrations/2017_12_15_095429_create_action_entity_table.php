<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_entity', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('entity_id')->nullable()->unsigned()->comment('ID сущности');
            $table->foreign('entity_id')->references('id')->on('entities');

            $table->bigInteger('action_id')->nullable()->unsigned()->comment('ID действия');
            $table->foreign('action_id')->references('id')->on('actions');

            $table->string('alias_action_entity')->index()->comment('Действие над сущностью');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
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
        Schema::dropIfExists('action_entity');
    }
}
