<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title')->nullable()->comment('Title');
            $table->string('h1')->nullable()->comment('H1');

            $table->text('description')->nullable()->comment('Description');
            $table->string('keywords')->nullable()->comment('Keywords');
            $table->text('content')->nullable()->comment('Content');

            $table->boolean('is_canonical')->default(0)->comment('Каноническая ссылка');

            $table->bigInteger('parent_id')->unsigned()->nullable()->comment('Id родителя');
            $table->foreign('parent_id')
                ->references('id')
                ->on('seos');

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
        Schema::dropIfExists('seos');
    }
}
