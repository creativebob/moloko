<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkflowsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflows_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название категории рабочего процесса');
            $table->string('slug')->index()->nullable()->comment('Слаг');
            $table->integer('level')->nullable()->unsigned()->comment('Уровень вложенности');

            $table->text('description')->nullable()->comment('Описание категории рабочего процесса');
            $table->text('seo_description')->nullable()->comment('Описание для сайта для категории рабочего процесса');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Id категории рабочего процесса');
            $table->foreign('parent_id')->references('id')->on('workflows_categories');

            $table->bigInteger('category_id')->unsigned()->nullable()->comment('Id категории, пишется каждому вложенному пункту');
            $table->foreign('category_id')->references('id')->on('workflows_categories');

            $table->bigInteger('processes_type_id')->nullable()->unsigned()->comment('Тип процесса');
            $table->foreign('processes_type_id')->references('id')->on('processes_types');

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
        Schema::dropIfExists('workflows_categories');
    }
}
