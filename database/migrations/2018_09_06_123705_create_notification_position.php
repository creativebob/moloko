<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_position', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('notification_id')->nullable()->unsigned()->comment('ID оповещения');
            // $table->foreign('notification_id')->references('id')->on('notifications');

            $table->integer('position_id')->nullable()->unsigned()->comment('ID должности');
            // $table->foreign('position_id')->references('id')->on('positions');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
