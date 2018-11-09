<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('challenges', function (Blueprint $table) {

            $table->integer('priority_id')->nullable()->unsigned()->comment('Приоритет');
            $table->foreign('priority_id')->references('id')->on('priorities');

        });
    }

    public function down()
    {

        Schema::table('challenges', function (Blueprint $table) {

            $table->dropForeign('challenges_priority_id_foreign');

        });
    }
}
