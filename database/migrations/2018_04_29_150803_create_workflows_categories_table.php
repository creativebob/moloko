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
            $table->increments('id');

            $table->string('name')->index()->comment('Название категории рабочего процесса');

            $table->text('description')->nullable()->comment('Описание категории рабочего процесса');
            $table->text('seo_description')->nullable()->comment('Описание для сайта для категории рабочего процесса');

            $table->integer('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->integer('parent_id')->nullable()->unsigned()->comment('Id категории рабочего процесса');
            $table->foreign('parent_id')->references('id')->on('workflows_categories');

            // $table->integer('workflows_mode_id')->nullable()->unsigned()->comment('Вид продукции');
            // $table->foreign('workflows_mode_id')->references('id')->on('workflows_modes');

            $table->integer('category_id')->unsigned()->nullable()->comment('Id категории, пишется каждому вложенному пункту');
            $table->foreign('category_id')->references('id')->on('workflows_categories');


            // Общие настройки
            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
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
