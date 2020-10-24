<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->morphs('contract');
            $table->morphs('document');

            $table->timestamp('registered_at')->nullable()->comment('Время регистрации');

            $table->decimal('cash', 10, 2)->default(0)->comment('Сумма наличного платежа');
            $table->decimal('electronically', 10, 2)->default(0)->comment('Сумма электронного платежа');

            $table->string('type')->comment('Тип');

            $table->decimal('total', 10, 2)->default(0)->comment('Итого оплачено');

            $table->decimal('cash_taken', 10, 2)->default(0)->comment('Принято наличных');
            $table->decimal('cash_change', 10, 2)->default(0)->comment('Сдача');

            $table->bigInteger('payments_method_id')->unsigned()->nullable()->comment('Id метода платежа');
            $table->foreign('payments_method_id')->references('id')->on('payments_methods');

            $table->bigInteger('currency_id')->unsigned()->nullable()->comment('Id валюты');
            $table->foreign('currency_id')->references('id')->on('currencies');


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
        Schema::dropIfExists('payments');
    }
}
