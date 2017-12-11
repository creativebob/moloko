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
            $table->integer('company_id')->unsigned()->comment('Id компании, в которой находится отдел');
            $table->integer('city_id')->unsigned()->comment('Id города, в котором находится отдел');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('department_name', 30)->unique()->index()->comment('Название отдела');
            $table->string('department_address', 100)->nullable()->comment('Адресс отдела');
            $table->bigInteger('department_phone')->nullable()->comment('Телефон отдела');
            $table->integer('department_parent_id')->unsigned()->nullable()->comment('Id отдела, в котором находится отдел');
            $table->foreign('department_parent_id')->references('id')->on('departments');
            $table->integer('filial_status')->comment('Маркер филиала, чтобы определить при поиске');
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
