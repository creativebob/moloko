<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_articles', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('goods_product_id')->nullable()->unsigned()->comment('ID товара');
            $table->foreign('goods_product_id')->references('id')->on('goods_products');

            $table->integer('manufacturer_id')->nullable()->unsigned()->comment('Id производителя артикула');
            $table->foreign('manufacturer_id')->references('id')->on('companies');

            $table->string('name')->nullable()->comment('Имя артикула');

            $table->text('description')->nullable()->comment('Описание артикула товара');

            $table->string('internal')->nullable()->comment('Имя генерируемого артикула');

            $table->integer('metrics_count')->nullable()->unsigned()->index()->comment('Количество метрик у артикула');
            $table->integer('compositions_count')->nullable()->unsigned()->index()->comment('Количество состава у артикула');

            $table->integer('draft')->nullable()->unsigned()->comment('Статус шаблона');
            $table->integer('archive')->nullable()->unsigned()->comment('Статус архива');


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
        Schema::dropIfExists('goods_articles');
    }
}
