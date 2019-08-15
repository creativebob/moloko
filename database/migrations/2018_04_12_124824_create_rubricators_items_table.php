<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRubricatorsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rubricators_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('rubricator_id')->nullable()->unsigned()->comment('Id рубрики');
            $table->foreign('rubricator_id')->references('id')->on('rubricators');

            $table->string('name')->index()->comment('Название');
            $table->string('slug')->index()->nullable()->comment('Слаг');
            $table->integer('level')->nullable()->unsigned()->comment('Уровень вложенности');

            $table->text('description')->nullable()->comment('Описание');
            $table->text('seo_description')->nullable()->comment('Описание для сайта');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Id родителя');
            $table->foreign('parent_id')->references('id')->on('rubricators_items');

            $table->bigInteger('category_id')->unsigned()->nullable()->comment('Id категории, пишется каждому вложенному пункту');
            $table->foreign('category_id')->references('id')->on('rubricators_items');


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
        Schema::dropIfExists('rubricators_items');
    }
}
