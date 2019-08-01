<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название группы артикулов');
            $table->text('description')->nullable()->comment('Описание группы артикулов');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            // Нужно будет удалить: тут unit_id не зачем
            $table->bigInteger('unit_id')->nullable()->unsigned()->comment('Id единицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->bigInteger('units_category_id')->nullable()->unsigned()->comment('Id категории единицы измерения');
            $table->foreign('units_category_id')->references('id')->on('units');

            // $table->bigInteger('rule_id')->nullable()->unsigned()->comment('Id правила определения цены');
            // $table->foreign('rule_id')->references('id')->on('rules');

            $table->bigInteger('album_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('album_id')->references('id')->on('albums');


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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles_groups');
    }
}
