<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');
            
            $table->integer('user_id')->unsigned()->nullable()->comment('Id пользователя');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('position_id')->unsigned()->nullable()->comment('Id должности');
            $table->foreign('position_id')->references('id')->on('positions');
            $table->integer('department_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('department_id')->references('id')->on('departments');
             $table->integer('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');
            

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
        Schema::dropIfExists('staff');
    }
}
