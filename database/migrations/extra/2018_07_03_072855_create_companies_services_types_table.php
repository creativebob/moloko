.<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesServicesTypesTable extends Migration
{

    public function up()
    {
        Schema::create('companies_services_types', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');
            
            $table->integer('services_type_id')->nullable()->unsigned()->comment('Id типа услуги');
            $table->foreign('services_type_id')->references('id')->on('services_types');

        });
    }

    public function down()
    {
        Schema::dropIfExists('companies_services_types');
    }
}
