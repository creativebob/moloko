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
            $table->bigIncrements('id');

            $table->string('name')->nullable()->index()->comment('Название категории периодов');
            $table->text('description')->nullable()->comment('Описание');

            $table->bigInteger('indicators_category_id')->unsigned()->nullable()->comment('Id категории индикаторов');
            $table->foreign('indicators_category_id')->references('id')->on('indicators_categories');

            $table->bigInteger('entity_id')->unsigned()->nullable()->comment('Id сущности');
            $table->foreign('entity_id')->references('id')->on('entities');

            $table->bigInteger('unit_id')->unsigned()->nullable()->comment('Id еденицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->bigInteger('period_id')->unsigned()->nullable()->comment('Id временного периода');
            $table->foreign('period_id')->references('id')->on('periods');

            $table->boolean('change_allowed')->default(0)->comment('Разрешение на изменение периода: true - разрешено, false - запрещено');

            $table->nullableMorphs('category');


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
        Schema::dropIfExists('indicators');
    }
}
