<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetricsTable extends Migration
{

    public function up()
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('Имя метрики');
            $table->text('description')->nullable()->comment('Описание метрики');

            $table->bigInteger('property_id')->nullable()->unsigned()->comment('ID свойства');
            $table->foreign('property_id')->references('id')->on('properties');

            $table->decimal('min', 15, 8)->nullable()->comment('Минимум');
            $table->decimal('max', 15, 8)->nullable()->comment('Максимум');

            $table->bigInteger('unit_id')->nullable()->unsigned()->comment('Id единицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->integer('decimal_place')->unsigned()->default(0)->comment('Знаков после запятой');

            $table->string('color')->nullable()->comment('Цвет');

            $table->string('boolean_false')->nullable()->comment('Отрицательный ответ');
            $table->string('boolean_true')->nullable()->comment('Положительный ответ');

             $table->string('list_type')->nullable()->comment('Тип списка');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(0)->comment('Отображение на сайте');
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
        Schema::dropIfExists('metrics');
    }
}
