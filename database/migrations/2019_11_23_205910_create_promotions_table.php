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

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
            $table->foreign('filial_id')->references('id')->on('departments');

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
