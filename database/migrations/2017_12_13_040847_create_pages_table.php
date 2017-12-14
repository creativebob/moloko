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
            $table->string('page_name')->index()->comment('Название страницы');
            $table->integer('site_id')->unsigned()->nullable()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');
            $table->string('page_title')->comment('Title для страницы');
            $table->text('page_description')->comment('Description для страницы');
            $table->string('page_alias')->index()->comment('Алиас');
            // $table->date('mydate')->comment('Тестовая дата');
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
        Schema::dropIfExists('pages');
    }
}
