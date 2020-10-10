<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOctober2020Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalogs_goods', function (Blueprint $table) {
            $table->boolean('is_access_page')->default(1)->comment('Страница товара')->after('slug');
            $table->boolean('is_check_stock')->default(0)->comment('Наличие на складе')->after('is_access_page');
        });

        Schema::table('catalogs_services', function (Blueprint $table) {
            $table->boolean('is_access_page')->default(1)->comment('Страница товара')->after('slug');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->boolean('is_autochange')->default(0)->comment('Авто-смена слайдов')->after('is_slider');
            $table->integer('delay')->nullable()->comment('Время задержки')->after('is_autochange');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalogs_goods', function (Blueprint $table) {
            $table->dropColumn([
                'is_access_page',
                'is_check_stock',
            ]);
        });

        Schema::table('catalogs_services', function (Blueprint $table) {
            $table->dropColumn([
                'is_access_page',
            ]);
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn([
                'is_autochange',
                'delay',
            ]);
        });
    }
}
