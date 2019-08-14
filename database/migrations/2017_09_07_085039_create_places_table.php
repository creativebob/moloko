<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesTable extends Migration
{

    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 40)->nullable()->index()->comment('Имя помещения');
            $table->text('description')->nullable()->comment('Описание для помещения');

            $table->bigInteger('phone')->unique()->nullable()->comment('Телефон компании');
            // $table->string('email')->nullable()->comment('Почта');

            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Адрес компании');
            $table->foreign('location_id')->references('id')->on('locations');

            // $table->integer('schedule_id')->nullable()->unsigned()->comment('Id графика работы');
            // $table->foreign('schedule_id')->references('id')->on('schedules');

            $table->integer('square')->nullable()->unsigned()->comment('Площадь в квадратных метрах');
            $table->integer('stockroom_status')->unsigned()->nullable()->comment('Флаг, что является складом');
            $table->integer('rent_status')->unsigned()->nullable()->comment('Флаг, что находится в аренде');


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
        Schema::dropIfExists('places');
    }
}
