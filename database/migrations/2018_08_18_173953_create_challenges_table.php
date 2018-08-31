<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            // $table->string('name')->index()->comment('Название задачи');

            $table->text('description')->nullable()->comment('Описание задачи');

            $table->integer('appointed_id')->nullable()->unsigned()->comment('ID пользователя, которому назначена задача');
            $table->foreign('appointed_id')->references('id')->on('users');

            $table->integer('finisher_id')->nullable()->unsigned()->comment('ID пользователя, завершил задачу');
            $table->foreign('finisher_id')->references('id')->on('users');

            $table->integer('challenges_id')->nullable()->unsigned()->comment('ID сущности');
            $table->string('challenges_type')->nullable()->comment('Сущность');

            $table->integer('challenges_type_id')->nullable()->unsigned()->comment('ID типа задачи');
            $table->foreign('challenges_type_id')->references('id')->on('challenges_types');

            $table->integer('status')->nullable()->unsigned()->comment('Статус');
            $table->integer('delegate_status')->nullable()->unsigned()->comment('Статус делигирования');

            $table->datetime('deadline_date')->nullable()->comment('Дата дедлайна');
            $table->datetime('completed_date')->nullable()->comment('Дата завершения');
            

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

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
        Schema::dropIfExists('challenges');
    }
}
