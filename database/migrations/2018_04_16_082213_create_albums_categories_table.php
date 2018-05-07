<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums_categories', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->index()->comment('Название категории альбомов');

            $table->integer('parent_id')->nullable()->unsigned()->comment('Id категории альбома');
            $table->foreign('parent_id')->references('id')->on('albums_categories');

            $table->integer('category_status')->unsigned()->nullable()->comment('Статус категории');

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
        Schema::dropIfExists('albums_categories');
    }
}
