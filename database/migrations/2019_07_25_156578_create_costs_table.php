<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->morphs('cmv');

            $table->bigInteger('manufacturer_id')->unsigned()->nullable()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

            $table->bigInteger('supplier_id')->unsigned()->nullable()->comment('Id поставщика');
            $table->foreign('supplier_id')->references('id')->on('suppliers');

            $table->string('serial')->nullable()->comment('Серийный номер');

            $table->integer('cost_average')->nullable()->unsigned()->comment('Среднее значение');
            $table->integer('cost_min')->nullable()->unsigned()->comment('Минимальное значение');
            $table->integer('cost_max')->nullable()->unsigned()->comment('Максимальное значение');

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
        Schema::dropIfExists('costs');
    }
}
