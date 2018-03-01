<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sectors', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');

            $table->string('sector_name')->index()->comment('Название области');
            $table->integer('sector_parent_id')->unsigned()->nullable()->comment('Id отдела, в котором находится отдел');
            $table->foreign('sector_parent_id')->references('id')->on('sectors');
            $table->integer('industry_status')->unsigned()->nullable()->comment('Статус категории');

            // $table->integer('industry_id')->unsigned()->nullable()->comment('Id категории, в которо1 находится сектор');
            // $table->foreign('industry_id')->references('id')->on('sectors');


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
        Schema::dropIfExists('sectors');
    }
}
