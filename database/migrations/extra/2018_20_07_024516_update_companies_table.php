<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {

            $table->boolean('external_control')->default(0)->comment('Внешнее управление');

        });
    }

    public function down()
    {

        Schema::table('companies', function (Blueprint $table) {

            $table->dropColumn('external_control');

        });
    }
}
