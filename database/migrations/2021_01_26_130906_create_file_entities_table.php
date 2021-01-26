<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_entities', function (Blueprint $table) {
            $table->bigInteger('file_id')->nullable()->unsigned()->comment('Id файла');
//            $table->foreign('file_id')->references('id')->on('files');

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
        Schema::dropIfExists('file_entities');
    }
}
