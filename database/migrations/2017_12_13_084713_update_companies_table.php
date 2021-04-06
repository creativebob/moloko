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
        Schema::table('companies', function(Blueprint $table) {


            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Адрес компании');
            $table->foreign('location_id')->references('id')->on('locations');

            $table->bigInteger('legal_location_id')->nullable()->unsigned()->comment('Юридический адрес компании');
            $table->foreign('legal_location_id')->references('id')->on('locations');

            $table->bigInteger('legal_form_id')->nullable()->unsigned()->comment('Правовая форма');
            $table->foreign('legal_form_id')->references('id')->on('legal_forms');

            $table->bigInteger('schedule_id')->nullable()->unsigned()->comment('Id графика работы');
            $table->foreign('schedule_id')->references('id')->on('schedules');

            $table->bigInteger('sector_id')->nullable()->unsigned()->comment('Id сектора');
            $table->foreign('sector_id')->references('id')->on('sectors');

	        $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id аватара');
	        $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('white_id')->nullable()->unsigned()->comment('white');
            $table->foreign('white_id')->references('id')->on('vectors');

            $table->bigInteger('black_id')->nullable()->unsigned()->comment('black');
            $table->foreign('black_id')->references('id')->on('vectors');

            $table->bigInteger('color_id')->nullable()->unsigned()->comment('color');
            $table->foreign('color_id')->references('id')->on('vectors');

            $table->bigInteger('taxation_type_id')->nullable()->unsigned()->comment('Тип системы налогообложения');
            $table->foreign('taxation_type_id')->references('id')->on('taxation_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('location_id');
            $table->dropForeign('companies_location_id_foreign');

            $table->dropColumn('legal_form_id');
            $table->dropForeign('companies_legal_form_id_foreign');

            $table->dropColumn('schedule_id');
            $table->dropForeign('companies_schedule_id_foreign');

            $table->dropColumn('sector_id');
            $table->dropForeign('companies_sector_id_foreign');
        });

    }
}
