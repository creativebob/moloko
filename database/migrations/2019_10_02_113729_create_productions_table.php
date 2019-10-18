<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->string('name')->nullable()->comment('Короткое название накладной');
            $table->text('description')->nullable()->comment('Описание');

            $table->date('receipt_date')->nullable()->comment('Дата приема');
            $table->string('number')->index()->nullable()->comment('Номер накладной');

            $table->bigInteger('manufacturer_id')->unsigned()->nullable()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('ID склада по умолчанию');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->decimal('amount', 12, 4)->default(0)->comment('Сумма');
            $table->boolean('draft')->default(1)->comment('Черновик');
            $table->boolean('is_produced')->default(0)->comment('Произведено');


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
        Schema::dropIfExists('productions');
    }
}
