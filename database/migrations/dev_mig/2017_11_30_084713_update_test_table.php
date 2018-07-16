<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test', function(Blueprint $table) {

            // Переименуем имя столбца name из родной таблицы Laravel в имя login
            $table->renameColumn('name', 'myname');
            $table->string('bug', 20)->nullable()->index()->comment('Жук');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('test', function (Blueprint $table) {
            $table->renameColumn('myname', 'name');
            $table->dropColumn('bug');
           
        });

    }
}
