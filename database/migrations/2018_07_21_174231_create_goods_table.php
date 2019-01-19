<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('goods_article_id')->nullable()->unsigned()->comment('ID артикула товара');
            $table->foreign('goods_article_id')->references('id')->on('goods_articles');

            $table->text('description')->nullable()->comment('Описание товара');

            $table->string('manually')->nullable()->comment('Имя для поиска (руками)');
            $table->string('external')->nullable()->comment('Имя внешнего артикула');

            $table->integer('cost')->nullable()->comment('Фиксированная себестоимость (руками)');
            $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим определения себестоимости');

            $table->integer('price')->nullable()->comment('Фиксированная цена (руками)');
            $table->integer('price_mode')->nullable()->unsigned()->comment('Режим определения цены');
            $table->integer('price_rule_id')->nullable()->unsigned()->comment('ID ценовой политики');
            $table->foreign('price_rule_id')->references('id')->on('price_rules');

            $table->integer('album_id')->nullable()->unsigned()->comment('ID альбома');
            $table->foreign('album_id')->references('id')->on('albums');

            $table->integer('photo_id')->nullable()->unsigned()->comment('ID аватара');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->string('portion_status')->nullable()->comment('Статус порции');
            $table->string('portion_name')->nullable()->comment('Имя порции');
            $table->string('portion_abbreviation')->nullable()->comment('Сокращение порции');
            $table->integer('portion_count')->nullable()->unsigned()->comment('Количество в порции');


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
        Schema::dropIfExists('goods');
    }
}
