<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update3Services extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prices_services', function (Blueprint $table) {
            $table->string('name_alt')->nullable()->comment('Альтернативное имя')->after('price');
            $table->string('external')->nullable()->comment('Внешний ID')->after('name_alt');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->bigInteger('price_unit_category_id')->nullable()->unsigned()->comment('Категория единицы измерения для определения цены')->after('category_id');
            $table->bigInteger('price_unit_id')->nullable()->unsigned()->comment('Единица измерения для определения цены')->after('price_unit_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prices_services', function (Blueprint $table) {
            $table->dropColumn([
                'name_alt',
                'external',
            ]);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'price_unit_category_id',
                'price_unit_id',
            ]);
        });
    }
}
