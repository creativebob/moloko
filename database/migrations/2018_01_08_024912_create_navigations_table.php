<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->comment('Имя категории меню');
            $table->string('alias')->nullable()->comment('Алиас категории меню');
            $table->string('css_class')->nullable()->comment('Имя класса для CSS');

            $table->bigInteger('site_id')->unsigned()->nullable()->comment('Id сайта');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->bigInteger('navigations_category_id')->nullable()->unsigned()->comment('ID категории навигации');
            $table->foreign('navigations_category_id')->references('id')->on('navigations_categories');

            $table->bigInteger('align_id')->unsigned()->nullable()->comment('Id сайта меню');
            $table->foreign('align_id')->references('id')->on('aligns');


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
        Schema::dropIfExists('navigations');
    }
}
