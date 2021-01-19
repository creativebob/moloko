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
        Schema::table('estimates', function (Blueprint $table) {
            $table->bigInteger('cancel_ground_id')->nullable()->unsigned()->comment('Id основания списания')->after('is_dismissed');

            $table->timestamp('produced_at')->nullable()->comment('Время производства')->after('registered_at');
        });

        Schema::table('catalogs_goods_items', function (Blueprint $table) {
            $table->string('video_url')->nullable()->comment('Ссылка на видео')->after('photo_id');
            $table->text('video')->nullable()->comment('Видео')->after('video_url');
        });

        Schema::table('catalogs_services_items', function (Blueprint $table) {
            $table->string('video_url')->nullable()->comment('Ссылка на видео')->after('photo_id');
            $table->text('video')->nullable()->comment('Видео')->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn('cancel_ground_id');

            $table->boolean('is_produced')->default(0)->comment('Произведено')->after('conducted_at');
        });

        Schema::table('catalogs_goods_items', function (Blueprint $table) {
            $table->dropColumn([
                'video_url',
                'video',
            ]);
        });

        Schema::table('catalogs_services_items', function (Blueprint $table) {
            $table->dropColumn([
                'video_url',
                'video',
            ]);
        });
    }
}
