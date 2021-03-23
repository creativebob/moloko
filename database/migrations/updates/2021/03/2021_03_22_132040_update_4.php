<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('name')->nullable()->comment('Название')->after('id');
            $table->string('external_id')->nullable()->comment('Идентификатор (ID)')->after('source_service_id');
            $table->string('page_public_url')->nullable()->comment('Публичная страница (ссылка)')->after('external_id');
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
                'name',
                'external_id',
                'page_public_url',
            ]);
        });
    }
}
