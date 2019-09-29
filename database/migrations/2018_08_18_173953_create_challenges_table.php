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
            $table->bigIncrements('id');

            // $table->string('name')->index()->comment('Название задачи');

            $table->text('description')->nullable()->comment('Описание задачи');

            $table->bigInteger('appointed_id')->nullable()->unsigned()->comment('ID пользователя, которому назначена задача');
            $table->foreign('appointed_id')->references('id')->on('users');

            $table->bigInteger('finisher_id')->nullable()->unsigned()->comment('ID пользователя, завершил задачу');
            $table->foreign('finisher_id')->references('id')->on('users');

            $table->bigInteger('subject_id')->nullable()->unsigned()->comment('ID озадаченной сущности');
            $table->string('subject_type')->nullable()->comment('Модель сущности');

            $table->bigInteger('challenges_type_id')->nullable()->unsigned()->comment('ID типа задачи');
            $table->foreign('challenges_type_id')->references('id')->on('challenges_types');

            $table->integer('status')->nullable()->unsigned()->comment('Статус');

            $table->bigInteger('priority_id')->nullable()->unsigned()->comment('Приоритет');
            $table->foreign('priority_id')->references('id')->on('priorities');

            $table->integer('delegate_status')->nullable()->unsigned()->comment('Статус делигирования');

            $table->datetime('deadline_date')->nullable()->comment('Дата дедлайна');
            $table->datetime('completed_date')->nullable()->comment('Дата завершения');


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
        Schema::dropIfExists('challenges');
    }
}
