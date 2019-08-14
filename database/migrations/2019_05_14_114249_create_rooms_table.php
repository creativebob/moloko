<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('article_id')->nullable()->unsigned()->comment('Id артикула');
            $table->foreign('article_id')->references('id')->on('articles');

            $table->bigInteger('category_id')->nullable()->unsigned()->comment('Id категории помещения');
            $table->foreign('category_id')->references('id')->on('rooms_categories');

            $table->boolean('archive')->default(0)->unsigned()->comment('Статус архива');

            $table->decimal('area', 15, 2)->nullable()->comment('Площадь (м2)');

            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Id локации');
            $table->foreign('location_id')->references('id')->on('locations');


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
        Schema::dropIfExists('rooms');
    }
}
