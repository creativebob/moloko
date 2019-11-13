<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserves', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('stock_id')->unsigned()->nullable()->comment('Id склада');
//            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
//            $table->foreign('filial_id')->references('id')->on('departments');

            $table->morphs('document');
            $table->morphs('documents_item');

            $table->morphs('cmv');

            $table->decimal('count', 12,4)->default(0)->comment('Количество');
//            $table->decimal('cost', 16, 8)->default(0)->comment('Стоимость');
//            $table->decimal('amount', 16, 8)->default(0)->comment('Сумма');

            $table->boolean('archive')->default(0)->comment('Архив');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
//            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(1)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
//            $table->foreign('author_id')->references('id')->on('users');

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
        Schema::dropIfExists('reserves');
    }
}
