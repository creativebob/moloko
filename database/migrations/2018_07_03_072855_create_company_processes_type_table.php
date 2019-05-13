.<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyProcessesTypeTable extends Migration
{

    public function up()
    {
        Schema::create('company_processes_type', function (Blueprint $table) {
            $table->bigInteger('company_id')->nullable()->unsigned()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');
            
            $table->bigInteger('processes_type_id')->nullable()->unsigned()->comment('Id типа процесса');
            $table->foreign('processes_type_id')->references('id')->on('processes_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_processes_type');
    }
}