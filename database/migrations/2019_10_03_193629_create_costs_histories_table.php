<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costs_histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('cost_id')->unsigned()->nullable()->comment('Id себестоимости');
            $table->foreign('cost_id')->references('id')->on('costs');

            $table->decimal('min', 12, 4)->default(0)->comment('Минимальное значение');
            $table->decimal('max', 12, 4)->default(0)->comment('Максимальное значение');
            $table->decimal('average', 16, 8)->default(0)->comment('Среднее значение');

            $table->timestamp('begin_date')->nullable()->comment('Дата и время начала');
            $table->timestamp('end_date')->nullable()->comment('Дата и время окончания');


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
        Schema::dropIfExists('costs_histories');
    }
}
