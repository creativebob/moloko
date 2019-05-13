<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooklistTypesTable extends Migration
{

    public function up()
    {
        Schema::create('booklist_types', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Имя типа списка');
            $table->string('entity_alias')->nullable()->index()->comment('Имя сущности для которой подходит текущий тип');
            $table->string('tag')->nullable()->nullable()->comment('Ключ для поиска');
            $table->text('description')->nullable()->comment('Описание типа списка');
            $table->boolean('change_allowed')->default(0)->comment('Разрешение на внесение изменений в список с данным типом. Параметр наследуется списку.');


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
        Schema::dropIfExists('booklist_types');
    }
}
