<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
//            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('outlet_id')->unsigned()->nullable()->comment('Id торговой точки');
//            $table->foreign('outlet_id')->references('id')->on('outlets');

            $table->date('date')->nullable()->comment('Дата');

            $table->timestamp('opened_at')->nullable()->comment('Дата и время открытия');
            $table->timestamp('closed_at')->nullable()->comment('Дата и время закрытия');

            $table->timestamp('need_closed_at')->nullable()->comment('Обязательное время закрытия');

            $table->decimal('balance_open', 10,2)->default(0)->comment('Баланс на момент открытия');
            $table->decimal('balance_close', 10,2)->default(0)->comment('Баланс на момент закрытия');

            $table->decimal('cash', 10, 2)->default(0)->comment('Сумма наличных платежей');
            $table->decimal('electronically', 10, 2)->default(0)->comment('Сумма электронных платежей');

            $table->boolean('is_opened')->default(0)->comment('Открытие');
            $table->boolean('is_reopened')->default(0)->comment('Переоткрытие');


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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
}
