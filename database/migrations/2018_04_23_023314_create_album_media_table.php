<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_media', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('album_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('album_id')->references('id')->on('albums');
            
            $table->integer('media_id')->nullable()->unsigned()->comment('Id медиа обьекта');
            $table->string('entity')->index()->comment('Сущность обьекта');

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
        Schema::dropIfExists('album_media');
    }
}
