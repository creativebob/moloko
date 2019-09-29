<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{

    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->integer('indicator_id')->nullable()->unsigned()->comment('ID индикатора');
            $table->integer('value')->nullable()->unsigned()->comment('Значение индикатора');

            $table->integer('year')->nullable()->unsigned()->comment('Год');
            $table->integer('month')->nullable()->unsigned()->comment('Месяц');
            $table->integer('week')->nullable()->unsigned()->comment('Неделя');
            $table->integer('day')->nullable()->unsigned()->comment('День');
            $table->integer('hour')->nullable()->unsigned()->comment('Час');
            $table->integer('minute')->nullable()->unsigned()->comment('Минута');
            $table->integer('second')->nullable()->unsigned()->comment('Секунда');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(1)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
