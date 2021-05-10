<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_flows', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
//            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('process_id')->nullable()->unsigned()->comment('Id события');
//            $table->foreign('process_id')->references('id')->on('events');

            $table->timestamp('start_at')->nullable()->comment('Дата начала');
            $table->timestamp('finish_at')->nullable()->comment('Дата окончания');
            $table->timestamp('started_at')->nullable()->comment('Дата фактического начала');
            $table->timestamp('finished_at')->nullable()->comment('Дата фактического окончания');

            $table->integer('capacity_min')->default(0)->comment('Необходимо людей');
            $table->integer('capacity_max')->default(0)->comment('Максимум людей');

            $table->bigInteger('manufacturer_id')->nullable()->unsigned()->comment('Id производителя');
//            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

            $table->bigInteger('initiator_id')->nullable()->unsigned()->comment('Id инициатора');
//            $table->foreign('initiator_id')->references('id')->on('services_flows');

            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Id локации');
//            $table->foreign('location_id')->references('id')->on('locations');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
//            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(1)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
//            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

            $table->timestamps();
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
        Schema::dropIfExists('events_flows');
    }
}
