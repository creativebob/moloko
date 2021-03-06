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
        Schema::table('menus', function (Blueprint $table) {
            $table->boolean('is_nofollow')->default(0)->comment('Запрет индексации')->after('text_hidden');
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->bigInteger('entities_type_id')->unsigned()->nullable()->comment('Id типа')->after('view_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('is_nofollow');
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->dropColumn('entities_type_id');
        });
    }
}
