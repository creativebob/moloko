<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tools_types', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('Название');
            $table->text('description')->nullable()->comment('Описание');

            $table->string('alias')->index()->nullable()->comment('Алиас');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tools_types');
    }
}
