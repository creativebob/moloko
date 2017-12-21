<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->comment('Id пользователя');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('position_id')->unsigned()->nullable()->comment('Id должности');
            $table->foreign('position_id')->references('id')->on('positions');
            $table->integer('department_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->integer('car_id')->unsigned()->nullable()->comment('Id автомобиля');
            // $table->foreign('car_id')->references('id')->on('cars');
            $table->date('date_employment')->nullable()->comment('Дата приема на работу');
            $table->date('date_dismissal')->nullable()->comment('Дата увольнения');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

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
        Schema::dropIfExists('employees');
    }
}
