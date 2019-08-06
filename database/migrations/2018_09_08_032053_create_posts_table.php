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
            $table->bigIncrements('id');

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
        Schema::dropIfExists('posts');
    }
}
