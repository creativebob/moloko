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

            $table->string('email')->nullable()->unique();

            $table->bigInteger('parent_id')->unsigned()->nullable()->comment('Id отдела, в котором находится отдел');
            $table->foreign('parent_id')->references('id')->on('departments');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала, пишется каждому отделу');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->text('code_map')->nullable()->comment('Код карты');

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
        Schema::dropIfExists('departments');
    }
}
