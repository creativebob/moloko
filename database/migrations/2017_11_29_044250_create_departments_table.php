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
            $table->increments('id');
            $table->integer('company_id')->nullable()->unsigned()->comment('Id компании, в которой находится отдел');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->integer('city_id')->nullable()->unsigned()->comment('Id города, в котором находится филиал/отдел');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('department_name', 60)->index()->comment('Название отдела');
            $table->string('department_address', 100)->nullable()->comment('Адресс отдела');
            $table->bigInteger('department_phone')->nullable()->comment('Телефон отдела');
            $table->integer('department_parent_id')->unsigned()->nullable()->comment('Id отдела, в котором находится отдел');
            $table->foreign('department_parent_id')->references('id')->on('departments');
            $table->integer('filial_status')->unsigned()->nullable()->comment('Маркер филиала, чтобы определить при поиске');
            $table->integer('filial_id')->unsigned()->nullable()->comment('Id филиала, пишется каждому отделу');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->timestamps();
            $table->integer('moderated')->nullable()->unsigned()->comment('Статус модерации');
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
