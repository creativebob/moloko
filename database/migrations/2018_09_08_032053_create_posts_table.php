<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->index()->comment('Название поста');

            // $table->integer('site_id')->unsigned()->nullable()->comment('Id сайта');
            // $table->foreign('site_id')->references('id')->on('sites');

            $table->string('title')->comment('Title для поста');
            $table->text('preview')->nullable()->comment('Превью для поста');
            $table->integer('photo_id')->nullable()->unsigned()->comment('Фото для превью для новости');
            $table->text('content')->nullable()->comment('Основной контент');

            $table->date('publish_begin_date')->index()->comment('Дата начала публикации');
            $table->date('publish_end_date')->nullable()->index()->comment('Дата окончания публикации');


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
        Schema::dropIfExists('posts');
    }
}
