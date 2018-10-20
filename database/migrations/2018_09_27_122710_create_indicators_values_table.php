<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndicatorsValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicators_values', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            // $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('indicator_id')->unsigned()->nullable()->comment('Id индикатора');
            // $table->foreign('indicator_id')->references('id')->on('indicators');

            $table->integer('period_id')->unsigned()->nullable()->comment('Id периода');
            // $table->foreign('period_id')->references('id')->on('periods');

            $table->string('value')->nullable()->index()->comment('Значение');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            // $table->foreign('author_id')->references('id')->on('users');

            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
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
        Schema::dropIfExists('indicators_values');
    }
}
