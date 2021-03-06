<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('production_id')->unsigned()->nullable()->comment('Id наряда');
            $table->foreign('production_id')->references('id')->on('productions');

            $table->morphs('cmv');

            $table->bigInteger('manufacturer_id')->unsigned()->nullable()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

	        $table->decimal('count', 12,4)->default(0)->comment('Количество');
            $table->decimal('cost', 12, 4)->default(0)->comment('Себестоимость еденицы');
	        $table->decimal('amount', 16, 8)->default(0)->comment('Сумма');

            $table->text('description')->nullable()->comment('Комментарий к позиции');

            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('ID склада на который приходовать ТМЦ');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('estimates_goods_item_id')->unsigned()->nullable()->comment('Id пункта сметы');
            $table->foreign('estimates_goods_item_id')->references('id')->on('estimates_goods_items');

            $table->bigInteger('entity_id')->nullable()->unsigned()->comment('Id сущности ТМЦ');
            $table->foreign('entity_id')->references('id')->on('entities');

            // Общие настройки
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
        Schema::dropIfExists('productions_items');
    }
}
