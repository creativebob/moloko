<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooklistsTable extends Migration
{

    public function up()
    {
        Schema::create('booklists', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->comment('Имя списка');
            $table->text('description')->nullable()->comment('Описание списка');

            $table->bigInteger('entity_id')->nullable()->unsigned()->comment('Id сущности');
            $table->foreign('entity_id')->references('id')->on('entities');

            $table->string('entity_alias')->index()->comment('Имя сущности');

            $table->boolean('change_allowed')->default(0)->comment('Разрешение на внесение изменений в список с данным типом. Параметр наследуется списку.');

            $table->bigInteger('booklist_type_id')->unsigned()->default(1)->comment('Тип списка');
            $table->foreign('booklist_type_id')->references('id')->on('booklist_types');

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booklists');
    }
}
