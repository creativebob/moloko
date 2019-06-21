<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Расположение филиала/отдела');
            $table->foreign('location_id')->references('id')->on('locations');

            $table->string('name', 60)->index()->comment('Название отдела');
            $table->bigInteger('phone')->nullable()->comment('Телефон отдела');

            $table->bigInteger('parent_id')->unsigned()->nullable()->comment('Id отдела, в котором находится отдел');
            $table->foreign('parent_id')->references('id')->on('departments');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала, пишется каждому отделу');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->text('code_map')->nullable()->comment('Код карты');

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
        Schema::dropIfExists('departments');
    }
}
