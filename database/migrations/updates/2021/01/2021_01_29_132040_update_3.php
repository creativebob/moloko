<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('slug')->nullable()->comment('Слаг')->after('name');
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->string('slug')->nullable()->comment('Слаг')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
            ]);
        });

        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
            ]);
        });
    }
}
