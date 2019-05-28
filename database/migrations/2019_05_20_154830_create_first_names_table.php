<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstNamesTable extends Migration
{

    public function up()
    {
        Schema::create('first_names', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('name', 40)->index()->comment('Полное имя');
            $table->boolean('gender', 40)->default(0)->comment('Признак пола (0 - мужской, 1 - женский)');

            $table->string('patronymic_male', 40)->nullable()->index()->comment('Отчество для мужчины');
            $table->string('patronymic_female', 40)->nullable()->index()->comment('Отчество для женщины');

            // Общие настройки

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

    public function down()
    {
        Schema::dropIfExists('first_names');
    }
}
