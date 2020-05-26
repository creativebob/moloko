<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position_role', function (Blueprint $table) {
            $table->bigInteger('position_id')->nullable()->unsigned()->comment('ID должности');
            $table->foreign('position_id')->references('id')->on('positions');

            $table->bigInteger('role_id')->nullable()->unsigned()->comment('ID категории пользователя');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('position_role');
    }
}
