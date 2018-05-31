<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorktimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worktimes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');

            $table->integer('schedule_id')->unsigned()->comment('Id графика работы');
            $table->foreign('schedule_id')->references('id')->on('schedules');

            $table->integer('weekday')->unsigned()->nullable()->comment('День недели');
            $table->integer('worktime_begin')->unsigned()->nullable()->comment('Время начала работы, сек');
            $table->integer('worktime_interval')->unsigned()->nullable()->comment('Интервал времени, сек');

            $table->integer('timeout')->unsigned()->nullable()->comment('Время начала перерыва, сек');
            $table->integer('timeout_interval')->unsigned()->nullable()->comment('Интервал перерыва, сек');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->comment('Поле для сортировки');
            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');

            $table->timestamps();
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('worktimes');
    }
}
