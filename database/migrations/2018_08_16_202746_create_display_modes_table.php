<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisplayModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('display_modes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index()->comment('Название');
            $table->string('alias')->index()->comment('Алиас');
            $table->string('class')->comment('Класс');
            $table->string('icon')->comment('Класс иконки');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('display_modes');
    }
}
