<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('outlets', function (Blueprint $table) {
//            $table->integer('extra_time')->unsigned()->default(0)->comment('Время доставки (для автовычисления), сек')->after('filial_id');
//        });

        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('points_rate', 10, 2)->default(0)->comment('Величина для начисления поинтов')->after('external_id');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('weight_unit', 9, 4)->default(0)->comment('Вес за единицу (кг)')->after('count');
            $table->decimal('volume_unit', 15, 8)->default(0)->comment('Обьем за единицу (м3)')->after('weight_unit');
        });

        Schema::table('offs', function (Blueprint $table) {
            $table->decimal('weight_unit', 9, 4)->default(0)->comment('Вес за единицу (кг)')->after('count');
            $table->decimal('volume_unit', 15, 8)->default(0)->comment('Обьем за единицу (м3)')->after('weight_unit');
        });

        Schema::table('reserves', function (Blueprint $table) {
            $table->decimal('weight_unit', 9, 4)->default(0)->comment('Вес за единицу (кг)')->after('count');
            $table->decimal('volume_unit', 15, 8)->default(0)->comment('Обьем за единицу (м3)')->after('weight_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('outlets', function (Blueprint $table) {
//            $table->dropColumn('extra_time');
//        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('points_rate');
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn([
                'weight_unit',
                'volume_unit',
            ]);
        });

        Schema::table('offs', function (Blueprint $table) {
            $table->dropColumn([
                'weight_unit',
                'volume_unit',
            ]);
        });

        Schema::table('reserves', function (Blueprint $table) {
            $table->dropColumn([
                'weight_unit',
                'volume_unit',
            ]);
        });
    }
}
