<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('page_name')->index()->comment('Псевдоним');
            $table->integer('site_id')->comment('Id сайта');
            $table->string('page_title')->comment('Title для страницы');
            $table->text('page_description')->comment('Description для страницы');
            $table->string('page_alias')->index()->comment('Алиас');
            $table->date('mydate')->comment('Тестовая дата');
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
        Schema::dropIfExists('pages');
    }
}
