<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicators', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            // $table->foreign('company_id')->references('id')->on('companies');

            $table->string('name')->nullable()->index()->comment('Название категории периодов');
            $table->text('description')->nullable()->comment('Описание');

            $table->integer('indicators_category_id')->unsigned()->nullable()->comment('Id категории индикаторов');
            // $table->foreign('indicators_category_id')->references('id')->on('indicators_categories');

            $table->integer('entity_id')->unsigned()->nullable()->comment('Id сущности');
            // $table->foreign('entity_id')->references('id')->on('entities');

            $table->integer('unit_id')->unsigned()->nullable()->comment('Id еденицы измерения');
            // $table->foreign('unit_id')->references('id')->on('units');

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
        Schema::dropIfExists('indicators');
    }
}
