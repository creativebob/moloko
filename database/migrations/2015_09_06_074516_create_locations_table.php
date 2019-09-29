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

            $table->integer('zip_code')->nullable()->comment('Почтовый индекс');
            $table->string('address')->nullable()->index()->comment('Адрес');

            $table->decimal('latitude', 11, 6)->nullable()->comment('Широта');
            $table->decimal('longitude', 11, 6)->nullable()->comment('Долгота');

            $table->integer('parse_count')->nullable()->unsigned()->comment('Количество прохождения записи парсером')->default('0');
            $table->integer('answer_count')->nullable()->unsigned()->comment('Количество пришедших ответов');


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
