<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('supplier_id')->unsigned()->nullable()->comment('Id поставщика');
            $table->foreign('supplier_id')->references('id')->on('suppliers');

            $table->string('name')->nullable()->comment('Короткое название накладной');
            $table->text('description')->nullable()->comment('Описание');

            $table->timestamp('receipt_date')->nullable()->comment('Дата приема');

            $table->string('number')->index()->nullable()->comment('Номер накладной');

            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('ID склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->integer('amount')->nullable()->comment('Сумма');

            $table->integer('draft')->unsigned()->nullable()->comment('Черновик');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

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
        Schema::dropIfExists('consignments');
    }
}
