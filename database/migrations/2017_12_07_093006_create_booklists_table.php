<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooklistsTable extends Migration
{

    public function up()
    {
        Schema::create('booklists', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->nullable()->comment('Имя списка');
            $table->text('description')->nullable()->comment('Описание списка');

            $table->integer('entity_id')->nullable()->unsigned()->comment('Id сущности');
            $table->foreign('entity_id')->references('id')->on('entities');

            $table->string('entity_alias')->index()->comment('Имя сущности');

            $table->boolean('change_allowed')->default(0)->comment('Разрешение на внесение изменений в список с данным типом. Параметр наследуется списку.');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->timestamps();
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
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
