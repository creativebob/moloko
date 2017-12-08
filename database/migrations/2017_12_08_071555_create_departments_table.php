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
            $table->string('department_name', 50)->index()->comment('Название отдела');
            $table->integer('department_parent_id')->unsigned()->nullable()->comment('Id отдела, в котором находится отдел');
            $table->foreign('department_parent_id')->references('id')->on('departments');
            $table->integer('filial_id')->unsigned()->nullable()->comment('Id филиала, в котором находится отдел');
            $table->foreign('filial_id')->references('id')->on('filials');
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
