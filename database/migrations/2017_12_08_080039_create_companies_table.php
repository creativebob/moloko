<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name', 40)->nullable()->index()->comment('Имя компании');

            $table->bigInteger('company_phone')->unique()->nullable()->comment('Телефон компании');
            $table->bigInteger('company_extra_phone')->nullable()->comment('Дополнительный телефон');

            $table->integer('city_id')->nullable()->unsigned()->comment('Id города');
            // $table->foreign('city_id')->references('id')->on('cities');        
            $table->string('company_address', 60)->nullable()->comment('Адрес компании');

            $table->bigInteger('company_inn')->nullable()->unsigned()->comment('ИНН компании');
            $table->bigInteger('kpp')->nullable()->unsigned()->comment('КПП');
            $table->string('account_settlement', 20)->nullable()->comment('Расчетный счет');
            $table->string('account_correspondent', 20)->nullable()->comment('Корреспондентский счет');
            $table->string('bank', 60)->nullable()->comment('Название банка');

            $table->integer('director_user_id')->nullable()->unsigned()->comment('Директор компании');
            // $table->foreign('user_id')->references('id')->on('users');

            $table->integer('admin_user_id')->nullable()->unsigned()->comment('Администратор компании');
            // $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('companies');
    }
}
