<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rights', function (Blueprint $table) {
            $table->increments('id');
            $table->string('right_name')->index()->unique()->comment('Простое имя правила для вывода');
            $table->string('right_action')->index()->unique()->comment('Метод - сущность (через дефис)');
            $table->integer('category_right_id')->nullable()->unsigned()->comment('Категория правила');
            $table->foreign('category_right_id')->references('id')->on('category_rights');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rights');
    }
}
