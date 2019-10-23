<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('lead_id')->unsigned()->nullable()->comment('Id лида');
            $table->foreign('lead_id')->references('id')->on('leads');

            $table->bigInteger('client_id')->unsigned()->nullable()->comment('Id клиента');
            $table->foreign('client_id')->references('id')->on('users');
	
	        $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
	        $table->foreign('filial_id')->references('id')->on('departments');

            $table->text('description')->nullable()->comment('Описание');

            $table->date('date')->nullable()->comment('Дата сметы');

            $table->string('number')->nullable()->comment('Номер сметы');

            $table->decimal('amount', 12, 4)->default(0)->comment('Сумма');
            $table->decimal('discount', 12, 4)->default(0)->comment('Скидка на заказ');
            $table->decimal('discount_percent', 5, 2)->default(0)->comment('Процент скидки');
            $table->decimal('total', 12, 4)->default(0)->comment('Итоговая сумма по заказу');

            // $table->decimal('discount_total', 12, 4)->default(0)->comment('Итоговая сумма всех скидок');

            $table->boolean('draft')->default(0)->unsigned()->comment('Черновик');
            $table->boolean('is_saled')->default(0)->comment('Продано');

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
        Schema::dropIfExists('estimates');
    }
}
