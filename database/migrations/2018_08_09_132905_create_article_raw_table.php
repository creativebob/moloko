<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleRawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_raw', function (Blueprint $table) {

            $table->bigInteger('article_id')->nullable()->unsigned()->comment('Id артикула');
            $table->foreign('article_id')->references('id')->on('articles');

            // $table->morphs('composition');

            $table->bigInteger('raw_id')->nullable()->unsigned()->comment('Id сырья');
            $table->foreign('raw_id')->references('id')->on('raws');

            $table->integer('value')->nullable()->unsigned()->comment('Значение');

            $table->integer('use')->nullable()->unsigned()->comment('Использование');
            $table->integer('leftover')->nullable()->unsigned()->comment('Остаток');
            $table->integer('waste')->nullable()->unsigned()->comment('Отходы ');

            $table->bigInteger('leftover_operation_id')->nullable()->unsigned()->comment('Id операции над остатком');
            $table->foreign('leftover_operation_id')->references('id')->on('leftover_operations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_raw');
    }
}
