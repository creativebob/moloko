<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название');

            $table->text('description')->nullable()->comment('Описание');

            $table->string('link')->nullable()->comment('Ссылка');

            $table->date('begin_date')->index()->comment('Дата начала');
            $table->date('end_date')->nullable()->index()->comment('Дата окончания');

            $table->char('mode', 11)->comment('Режим');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Фото');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('tiny_id')->nullable()->unsigned()->comment('tiny');
            $table->foreign('tiny_id')->references('id')->on('photos');

            $table->bigInteger('small_id')->nullable()->unsigned()->comment('small');
            $table->foreign('small_id')->references('id')->on('photos');

            $table->bigInteger('medium_id')->nullable()->unsigned()->comment('medium');
            $table->foreign('medium_id')->references('id')->on('photos');

            $table->bigInteger('large_id')->nullable()->unsigned()->comment('large');
            $table->foreign('large_id')->references('id')->on('photos');

            $table->bigInteger('large_x_id')->nullable()->unsigned()->comment('large_x');
            $table->foreign('large_x_id')->references('id')->on('photos');

            $table->text('horizontal')->nullable()->comment('horizontal');
            $table->text('vertical')->nullable()->comment('vertical');
            $table->text('square')->nullable()->comment('square');

            $table->string('prom')->nullable()->comment('Триггер для отображения');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->bigInteger('site_id')->unsigned()->nullable()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->boolean('is_slider')->default(false)->comment('Отображение слайдера');
            $table->boolean('is_recommend')->default(false)->comment('Отображение рекомендации');
            $table->boolean('is_upsale')->default(false)->comment('Отображение на корзине');

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
        Schema::dropIfExists('promotions');
    }
}
