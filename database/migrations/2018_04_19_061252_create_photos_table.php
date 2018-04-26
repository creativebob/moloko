<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->index()->comment('Название фото');
            $table->string('title')->nullable()->comment('Заголовок фото');
            $table->string('description')->nullable()->comment('Описание фото');

            $table->string('path')->nullable()->comment('Путь к фото');
            $table->string('alias')->nullable()->comment('Алиас фото');

            $table->integer('width')->comment('Ширина фото');
            $table->integer('height')->comment('Высота фото');
            $table->decimal('size', 10, 2)->comment('Размер фото');
            $table->string('extension')->comment('Расширение фото');

            $table->integer('photo_access')->nullable()->unsigned()->comment('0 - личный, 1 - публичный');
            
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
        Schema::dropIfExists('photos');
    }
}
