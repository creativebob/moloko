<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название категории единицы измерения');
            // $table->text('description')->nullable()->comment('Описание категории единицы измерения');

            // $table->string('unit')->comment('Единица измерения');
            // $table->string('abbreviation')->comment('Сокращенное название категории');

            $table->boolean('article')->default(0)->comment('Относится к артикулам');
            $table->boolean('process')->default(0)->comment('Относится к процессам');

            $table->string('alias')->index()->nullable()->comment('Алиас категории');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(0)->comment('Отображение на сайте');
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
        Schema::dropIfExists('units_categories');
    }
}
