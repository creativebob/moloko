<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('alt')->nullable()->comment('Alt')->after('mode');
            $table->string('title')->nullable()->comment('Title')->after('alt');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->string('alt')->nullable()->comment('Alt')->after('photo_id');
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->string('alt')->nullable()->comment('Alt')->after('description');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn([
                'alt',
                'title',
            ]);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn([
                'alt',
            ]);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn([
                'alt',
            ]);
        });
    }
}
