<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiltersGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filters_goods', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->index()->comment('Название сайта');
            $table->text('description')->nullable()->comment('Описание');

            $table->bigInteger('metric_id')->unsigned()->nullable()->comment('Id метрики');
            $table->foreign('metric_id')->references('id')->on('metrics');

            $table->bigInteger('catalogs_goods_item_id')->unsigned()->nullable()->comment('Id раздела каталога');
            $table->foreign('catalogs_goods_item_id')->references('id')->on('catalogs_goods_items');


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
        Schema::dropIfExists('filters_goods');
    }
}
