<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->nullableMorphs('subscriberable');

            $table->bigInteger('client_id')->unsigned()->nullable()->comment('Id склиента');
//            $table->foreign('client_id')->references('id')->on('clients');

            $table->string('name')->nullable()->comment('Имя');
            $table->string('email')->nullable()->comment('Email');

            $table->timestamp('denied_at')->nullable()->comment('Запрет');
            $table->boolean('is_active')->default(1)->comment('Активный');

            $table->bigInteger('site_id')->unsigned()->nullable()->comment('Id сайта');
//            $table->foreign('site_id')->references('id')->on('sites');

            $table->string('token', 30)->unique()->comment('Токен');

            $table->timestamp('archived_at')->nullable()->comment('Архив');

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
        Schema::dropIfExists('subscribers');
    }
}
