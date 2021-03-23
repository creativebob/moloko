<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{

    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->comment('Название');
            $table->text('description')->nullable()->comment('Комментарий к аккаунту');

            $table->string('login')->comment('Логин');
            $table->string('password')->nullable()->comment('Пароль');
            $table->string('api_token', 200)->nullable()->comment('Токен');
            $table->string('secret', 200)->nullable()->comment('Секрет');

            $table->string('alias')->nullable()->index()->comment('Алиас аккаунта');

            $table->bigInteger('source_service_id')->unsigned()->nullable()->comment('Id источника - внешний сервис');
            $table->foreign('source_service_id')->references('id')->on('source_services');

            $table->string('external_id')->nullable()->comment('Идентификатор (ID)');
            $table->string('page_public_url')->nullable()->comment('Публичная страница (ссылка)');


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

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
