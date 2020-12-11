<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название');
            $table->text('description')->nullable()->comment('Описание');

            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Id локации');
//            $table->foreign('location_id')->references('id')->on('locations');

            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('Id склада');
//            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('template_id')->nullable()->unsigned()->comment('Id шаблона чека');
//            $table->foreign('template_id')->references('id')->on('templates');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
//            $table->foreign('filial_id')->references('id')->on('departments');

            $table->integer('extra_time')->unsigned()->default(0)->comment('Время доставки (для автовычисления), сек');


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
        Schema::dropIfExists('outlets');
    }
}
