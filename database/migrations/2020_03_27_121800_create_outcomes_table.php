<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutcomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outcomes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название');
            $table->text('description')->nullable()->comment('Описание');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('Id аватара');
            $table->foreign('photo_id')->references('id')->on('photos');

            $table->bigInteger('category_id')->nullable()->unsigned()->comment('Id категории');
            $table->foreign('category_id')->references('id')->on('outcomes_categories');

            $table->date('begin_date')->index()->comment('Дата начала');
            $table->date('end_date')->nullable()->index()->comment('Дата окончания');

            $table->bigInteger('client_id')->nullable()->unsigned()->comment('Id клиента');
            $table->foreign('client_id')->references('id')->on('clients');

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
        Schema::dropIfExists('outcomes');
    }
}
