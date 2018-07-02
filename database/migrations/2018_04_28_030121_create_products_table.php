<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            // $table->integer('product_id')->nullable()->unsigned()->comment('ID товара');
            // $table->foreign('product_id')->references('id')->on('products');

            $table->string('name')->nullable()->comment('Имя артикула (руками)');

            $table->string('photo_id')->index()->nullable()->comment('Обложка артикула');
            // $table->foreign('photo_id')->references('id')->on('photos');

            $table->string('description')->index()->nullable()->comment('Описание артикула');

            // $table->integer('unit_id')->nullable()->unsigned()->comment('ID еденицы измерения');
            // $table->foreign('unit_id')->references('id')->on('units');

            $table->integer('rule_id')->nullable()->unsigned()->comment('ID правила определения цены');
            // $table->foreign('rule_id')->references('id')->on('rules');

            $table->integer('products_category_id')->nullable()->unsigned()->comment('Id категории в которой находиться товар');
            $table->foreign('products_category_id')->references('id')->on('products_categories');

            $table->integer('manufacturer_id')->nullable()->unsigned()->comment('Id производителя товара');
            $table->foreign('manufacturer_id')->references('id')->on('companies');

            $table->integer('album_id')->nullable()->unsigned()->comment('ID альбома');
            $table->foreign('album_id')->references('id')->on('albums');

            $table->string('internal')->nullable()->comment('Имя генерируемого артикула');
            $table->string('external')->nullable()->comment('Имя внешнего артикула');

            $table->integer('cost')->nullable()->comment('Фиксированная себестоимость товара (руками)');
            $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим определения себестоимости');

            $table->integer('price')->nullable()->comment('Фиксированная цена товара (руками)');
            $table->integer('price_mode')->nullable()->unsigned()->comment('Режим определения цены');
            $table->integer('price_rule_id')->nullable()->unsigned()->comment('ID ценовой политики');
            $table->foreign('price_rule_id')->references('id')->on('price_rules');

            $table->integer('metrics_count')->nullable()->unsigned()->index()->comment('Количество метрик у артикула');
            $table->integer('compositions_count')->nullable()->unsigned()->index()->comment('Количество состава у артикула');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

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
        Schema::dropIfExists('products');
    }
}
