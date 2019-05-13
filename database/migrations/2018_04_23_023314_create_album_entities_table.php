<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumEntitiesTable extends Migration
{

    public function up()
    {
        Schema::create('album_entities', function (Blueprint $table) {

            $table->bigInteger('album_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('album_id')->references('id')->on('albums');

            $table->morphs('album_entities');

            // $table->bigInteger('album_entities_id')->nullable()->unsigned()->comment('Id сущности связанной с альбомом');
            // $table->string('album_entities_type')->index()->comment('Сущность обьекта');
        });
    }

    public function down()
    {
        Schema::dropIfExists('album_entities');
    }
}


