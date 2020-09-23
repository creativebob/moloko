<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('representatives', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->nullable()->comment('Id пользователя');
//            $table->foreign('user_id')->references('id')->on('users');

            $table->bigInteger('organization_id')->unsigned()->nullable()->comment('Id организации');
//            $table->foreign('organization_id')->references('id')->on('companies');

            $table->text('description')->nullable()->comment('Описание');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
//            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('representatives');
    }
}
