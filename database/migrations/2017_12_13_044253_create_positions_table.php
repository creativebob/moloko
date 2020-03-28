<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название');

            $table->text('description')->nullable()->comment('Описание');

            $table->bigInteger('page_id')->unsigned()->nullable()->comment('Id страницы приземления');
            $table->foreign('page_id')->references('id')->on('pages');

            $table->boolean('direction')->default(0)->unsigned()->comment('Руководящая должность');

            $table->bigInteger('sector_id')->nullable()->unsigned()->comment('Id сектора');
            $table->foreign('sector_id')->references('id')->on('sectors');

            $table->boolean('archive')->default(0)->unsigned()->comment('Архив');


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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('positions');
    }
}
