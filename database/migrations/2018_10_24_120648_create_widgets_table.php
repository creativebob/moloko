<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetsTable extends Migration
{

    public function up()
    {
        Schema::create('widgets', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->index()->comment('Название');
            $table->text('description')->nullable()->comment('Описание');

            $table->string('tag')->nullable()->index()->comment('Тег');
            $table->string('method')->nullable()->comment('Метод');
        });
    }

    public function down()
    {
        Schema::dropIfExists('widgets');
    }
}
