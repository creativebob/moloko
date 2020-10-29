<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название');
            $table->text('description')->nullable()->comment('Описание');

            $table->string('subject')->nullable()->comment('Текст');
            $table->string('from_name')->nullable()->comment('От кого (имя)');
            $table->string('from_email')->nullable()->comment('Откуда (email)');

            $table->bigInteger('template_id')->unsigned()->nullable()->comment('Id шаблона');
//            $table->foreign('template_id')->references('id')->on('templates');

            $table->bigInteger('mailing_list_id')->unsigned()->nullable()->comment('Id списка рассылки');
//            $table->foreign('mailing_list_id')->references('id')->on('mailing_lists');

            $table->boolean('is_active')->default(0)->comment('Рассылка');

            $table->timestamp('started_at')->nullable()->comment('Установленное время начала');
            $table->timestamp('begined_at')->nullable()->comment('Время фактического начала');
            $table->timestamp('ended_at')->nullable()->comment('Время фактического окончания');

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
        Schema::dropIfExists('mailings');
    }
}
