<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetricsTable extends Migration
{

    public function up()
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->comment('Имя метрики');
            $table->text('description')->nullable()->comment('Описание метрики');

            $table->integer('property_id')->nullable()->unsigned()->comment('ID свойства');
            $table->foreign('property_id')->references('id')->on('properties');

            $table->decimal('min', 15, 8)->nullable()->comment('Минимум');
            $table->decimal('max', 15, 8)->nullable()->comment('Максимум');

            $table->integer('unit_id')->nullable()->unsigned()->comment('Id единицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->integer('decimal_place')->unsigned()->default(0)->comment('Знаков после запятой');

            $table->string('color')->nullable()->comment('Цвет');

            $table->string('boolean_false')->nullable()->comment('Отрицательный ответ');
            $table->string('boolean_true')->nullable()->comment('Положительный ответ');

             $table->string('list_type')->nullable()->comment('Тип списка');


            // Общие настройки
            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down()
    {
        Schema::dropIfExists('metrics');
    }
}
