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
            // $table->foreign('company_id')->references('id')->on('companies');

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
            
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            // $table->foreign('author_id')->references('id')->on('users');

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
