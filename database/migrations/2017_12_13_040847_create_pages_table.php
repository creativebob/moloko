<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('site_id')->unsigned()->nullable()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->string('name')->index()->comment('Название страницы');
            $table->string('subtitle')->nullable()->comment('Подзаголовок для страницы');
            $table->string('alias')->index()->comment('Алиас');
            $table->string('slug')->index()->nullable()->comment('Слаг');

            $table->bigInteger('seo_id')->nullable()->unsigned()->comment('Id seo');
            $table->foreign('seo_id')
                ->references('id')
                ->on('seos');

            $table->string('title')->nullable()->comment('Title для страницы');
            $table->string('header')->nullable()->comment('Header для страницы');
            $table->text('description')->nullable()->comment('Description для страницы');
            $table->string('keywords')->nullable()->comment('Ключевые слова');
            $table->text('content')->nullable()->comment('Контент страницы');

            $table->string('video_url')->nullable()->comment('Ссылка на видео');
            $table->text('video')->nullable()->comment('Код вставки видео');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Фотография');
            $table->foreign('photo_id')->references('id')->on('photos');


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
        Schema::dropIfExists('pages');
    }
}
