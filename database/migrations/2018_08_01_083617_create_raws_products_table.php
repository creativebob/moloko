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
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->index()->comment('Название сырья');

            $table->string('photo_id')->index()->nullable()->comment('Обложка сырья');
            // $table->foreign('photo_id')->references('id')->on('photos');

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

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');

            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
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
