<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainFilialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_filial', function (Blueprint $table) {
            $table->bigInteger('domain_id')->nullable()->unsigned()->comment('Id домена');
            $table->foreign('domain_id')->references('id')->on('domains');

            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->boolean('main')->default(0)->comment('Главный филиал');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domain_filial');
    }
}
