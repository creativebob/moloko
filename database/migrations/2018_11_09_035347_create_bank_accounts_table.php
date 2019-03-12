<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountsTable extends Migration
{

    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('holder_id')->unsigned()->nullable()->comment('Компания держатель банковского счета');
            $table->foreign('holder_id')->references('id')->on('companies');

            $table->integer('bank_id')->nullable()->unsigned()->comment('Id банка');
            $table->foreign('bank_id')->references('id')->on('banks');

            $table->integer('archive')->nullable()->unsigned()->comment('Статус архива');

            // $table->string('name')->index()->comment('Имя банковского аккаунта');
            // $table->text('description')->comment('Описание');

            $table->string('account_settlement', 20)->nullable()->comment('Расчетный счет');
            $table->string('account_correspondent', 20)->nullable()->comment('Корреспондентский счет');


            // Общие настройки
            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}
