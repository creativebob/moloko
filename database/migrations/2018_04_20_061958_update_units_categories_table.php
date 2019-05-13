<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUnitsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('units_categories', function (Blueprint $table) {
            $table->bigInteger('unit_id')->unsigned()->nullable()->comment('Id юнита');
            // $table->foreign('unit_id')->references('id')->on('units');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('units_categories', function (Blueprint $table) {
            $table->dropColumn('unit_id');
            // $table->dropForeign('units_categories_unit_id_foreign');
        });
    }
}
