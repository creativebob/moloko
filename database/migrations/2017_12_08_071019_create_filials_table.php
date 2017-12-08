<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->unsigned()->comment('Id города, в котором находится филиал');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('filial_name', 30)->unique()->index()->comment('Название филиала');
            $table->string('filial_address', 100)->unique()->comment('Адресс филиала');
            $table->bigInteger('filial_phone')->unique()->nullable()->comment('Телефон филиала');
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
        Schema::dropIfExists('filials');
    }
}
