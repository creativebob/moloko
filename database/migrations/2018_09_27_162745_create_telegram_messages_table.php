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
            $table->bigIncrements('id');

            $table->integer('message_id')->nullable()->unsigned();
            $table->integer('update_id')->nullable()->unsigned();

            $table->integer('from_id')->nullable();
            $table->boolean('from_is_bot');
            $table->string('from_first_name')->nullable();
            $table->string('from_last_name')->nullable();
            $table->string('from_username')->nullable();
            $table->string('from_language_code')->nullable();

            $table->integer('chat_id')->nullable()->unsigned();
            $table->string('chat_first_name')->nullable();
            $table->string('chat_last_name')->nullable();
            $table->string('chat_username')->nullable();
            $table->string('chat_type')->nullable();

            $table->text('message')->nullable();
            $table->integer('date')->nullable()->unsigned();


            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(0)->comment('Отображение на сайте');
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
        Schema::dropIfExists('telegram_messages');
    }
}
