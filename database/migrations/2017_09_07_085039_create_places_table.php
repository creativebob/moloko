<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{

    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 40)->nullable()->index()->comment('Имя помещения');
            $table->text('description')->nullable()->comment('Описание для помещения');

            $table->bigInteger('phone')->unique()->nullable()->comment('Телефон компании');
            // $table->string('email')->nullable()->comment('Почта');

            $table->integer('location_id')->nullable()->unsigned()->comment('Адрес компании');
            $table->foreign('location_id')->references('id')->on('locations');

            // $table->integer('schedule_id')->nullable()->unsigned()->comment('Id графика работы');
            // $table->foreign('schedule_id')->references('id')->on('schedules');

            $table->integer('square')->nullable()->unsigned()->comment('Площадь в квадратных метрах');
            $table->integer('stockroom_status')->unsigned()->nullable()->comment('Флаг, что является складом');
            $table->integer('rent_status')->unsigned()->nullable()->comment('Флаг, что находится в аренде');


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
        Schema::dropIfExists('places');
    }
}
