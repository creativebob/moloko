<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raws_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название сырья');

            $table->integer('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->enum('set_status', ['one', 'set'])->comment('Статус набора (Один/набор)');

            $table->text('description')->nullable()->comment('Описание сырья');

            $table->integer('unit_id')->nullable()->unsigned()->comment('ID еденицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->integer('rule_id')->nullable()->unsigned()->comment('ID правила определения цены');
            // $table->foreign('rule_id')->references('id')->on('rules');

            $table->integer('raws_category_id')->nullable()->unsigned()->comment('Id категории в которой находиться сырье');
            $table->foreign('raws_category_id')->references('id')->on('raws_categories');

            $table->integer('album_id')->nullable()->unsigned()->comment('ID альбома');
            $table->foreign('album_id')->references('id')->on('albums');


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
        Schema::dropIfExists('raws_products');
    }
}
