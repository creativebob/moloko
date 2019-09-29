<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountsTable extends Migration
{

    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('holder_id')->unsigned()->nullable()->comment('Компания держатель банковского счета');
            $table->foreign('holder_id')->references('id')->on('companies');

            $table->bigInteger('bank_id')->nullable()->unsigned()->comment('Id банка');
            $table->foreign('bank_id')->references('id')->on('companies');

            $table->integer('archive')->nullable()->unsigned()->comment('Статус архива');

            $table->string('account_settlement', 20)->nullable()->comment('Расчетный счет');
            $table->string('account_correspondent', 20)->nullable()->comment('Корреспондентский счет');

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
        Schema::dropIfExists('bank_accounts');
    }
}
