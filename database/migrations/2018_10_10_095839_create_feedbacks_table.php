<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->bigIncrements('id');


            $table->bigInteger('site_id')->unsigned()->nullable()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->nullableMorphs('feedback');

            $table->text('body')->nullable()->comment('Тело отзыва');

            $table->string('person')->nullable()->comment('Имя автора отзыва');
            $table->string('job')->nullable()->comment('Статус автора отзыва');

            $table->date('call_date')->nullable()->comment('Дата обращения');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('ID аватара');
            $table->foreign('photo_id')->references('id')->on('photos');


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
        Schema::dropIfExists('feedbacks');
    }
}
