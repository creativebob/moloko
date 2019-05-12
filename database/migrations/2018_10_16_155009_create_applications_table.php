<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{

    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('supplier_id')->unsigned()->nullable()->comment('Id поставщика');
            $table->foreign('supplier_id')->references('id')->on('suppliers');

            $table->string('name')->index()->comment('Короткое название заявки');
            $table->text('description')->nullable()->comment('Описание');

            $table->timestamp('send_date')->nullable()->comment('Дата отправки заявки');

            $table->string('number')->nullable()->comment('Номер заявки');

            $table->bigInteger('stage_id')->nullable()->unsigned()->comment('ID этапа');
            $table->foreign('stage_id')->references('id')->on('stages');

            $table->integer('draft')->unsigned()->nullable()->comment('Черновик');


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

    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
