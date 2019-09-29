<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processes_groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название группы процессов');
            $table->text('description')->nullable()->comment('Описание группы процессов');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id фото (аватар)');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('unit_id')->nullable()->unsigned()->comment('Id единицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->integer('rule_id')->nullable()->unsigned()->comment('Id правила определения цены');
            // $table->foreign('rule_id')->references('id')->on('rules');

            $table->bigInteger('album_id')->nullable()->unsigned()->comment('Id альбома');
            $table->foreign('album_id')->references('id')->on('albums');


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(1)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

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
        Schema::dropIfExists('processes_groups');
    }
}
