<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToolsStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tools_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('cmv_id')->nullable()->unsigned()->comment('Id инструмента');
            $table->foreign('cmv_id')->references('id')->on('tools');

            $table->decimal('count', 12,4)->default(0)->comment('Количество');
            $table->decimal('reserve', 12,4)->default(0)->comment('Резерв');
            $table->decimal('free', 12,4)->default(0)->comment('Свободно');

            $table->decimal('weight', 9, 4)->default(0)->comment('Вес (кг)');
            $table->decimal('volume', 15, 8)->default(0)->comment('Обьем (м3)');

            $table->string('serial')->nullable()->comment('Серийный номер');

            $table->bigInteger('manufacturer_id')->nullable()->unsigned()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');


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
        Schema::dropIfExists('tools_stocks');
    }
}
