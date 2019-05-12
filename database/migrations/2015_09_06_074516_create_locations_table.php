<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('city_id')->nullable()->unsigned()->comment('Id города');
            $table->foreign('city_id')->references('id')->on('cities');

            $table->bigInteger('country_id')->nullable()->unsigned()->comment('Id страны');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->string('address')->nullable()->index()->comment('Адрес');

            $table->decimal('latitude', 11, 6)->nullable()->comment('Широта');
            $table->decimal('longitude', 11, 6)->nullable()->comment('Долгота');

            $table->integer('parse_count')->nullable()->unsigned()->comment('Количество прохождения записи парсером')->default('0');
            $table->integer('answer_count')->nullable()->unsigned()->comment('Количество пришедших ответов');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

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
        Schema::dropIfExists('locations');
    }
}
