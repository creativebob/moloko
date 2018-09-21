<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_entities', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('phone_id')->unsigned()->comment('Id телефона');
            $table->foreign('phone_id')->references('id')->on('phones');
            
            $table->integer('phone_entity_id')->unsigned()->comment('Id сущности');
            $table->string('phone_entity_type')->comment('Сущность');

            $table->integer('delivery')->nullable()->comment('Согласие на рассылку (1 - не согласен / null - согласен)');
            $table->integer('main')->nullable()->comment('Тип телефона ( 1 - основной / null - добавочный)');
            $table->integer('archive')->nullable()->comment('Архив');

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
        Schema::dropIfExists('phone_entities');
    }
}
