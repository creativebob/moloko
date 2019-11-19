<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->comment('Название');
            $table->string('abbreviation')->nullable()->comment('Аббревиатура');
            $table->string('short')->nullable()->comment('Короткое');
            $table->string('symbol')->nullable()->comment('Символ');

            $table->decimal('points', 12, 4)->default(0)->comment('ВНутренняя валюта');


            // Общие настройки
//            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
//            $table->foreign('company_id')->references('id')->on('companies');
//
//            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
//            $table->boolean('display')->default(1)->comment('Отображение на сайте');
//            $table->boolean('system')->default(0)->comment('Системная запись');
//            $table->boolean('moderation')->default(0)->comment('Модерация');
//
//            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
//            $table->foreign('author_id')->references('id')->on('users');
//
//            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
//
//            $table->timestamps();currencies
//            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
