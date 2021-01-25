<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название новости');
            // $table->string('title')->comment('Название новости для новости');

            $table->string('alias')->index()->nullable()->comment('Алиас');
            $table->string('slug')->index()->nullable()->comment('Слаг');

            $table->text('preview')->nullable()->comment('Превью для новости');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (превью)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->string('alt')->nullable()->comment('Alt');

            $table->text('content')->nullable()->comment('Контент новости');

            $table->date('publish_begin_date')->index()->comment('Дата начала публикации');
            $table->date('publish_end_date')->nullable()->index()->comment('Дата окончания публикации');

            $table->bigInteger('rubricator_id')->unsigned()->nullable()->comment('Id рубрики');
            $table->foreign('rubricator_id')->references('id')->on('rubricators');

            $table->bigInteger('rubricators_item_id')->unsigned()->nullable()->comment('Id пункта рубрики');
            $table->foreign('rubricators_item_id')->references('id')->on('rubricators_items');

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
        Schema::dropIfExists('news');
    }
}
