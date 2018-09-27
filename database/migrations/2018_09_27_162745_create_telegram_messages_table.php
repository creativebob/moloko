<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelegramMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('message_id')->nullable()->unsigned();
            $table->integer('update_id')->nullable()->unsigned();

            $table->integer('f_chat_id')->nullable()->unsigned();
            $table->string('f_first_name')->nullable()->index();
            $table->string('f_last_name')->nullable()->index();
            $table->string('f_username')->nullable()->index();

            $table->integer('chat_id')->nullable()->unsigned();
            $table->string('first_name')->nullable()->index();
            $table->string('last_name')->nullable()->index();
            $table->string('username')->nullable()->index();

            $table->text('message')->nullable();
            
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
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
        Schema::dropIfExists('telegram_messages');
    }
}
