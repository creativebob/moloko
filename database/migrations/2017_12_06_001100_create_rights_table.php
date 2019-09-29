<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index()->comment('Простое имя правила для вывода');

            $table->string('object_entity')->index()->comment('ID сущности (Полиморфно)');

            $table->bigInteger('category_right_id')->nullable()->unsigned()->comment('Категория правила');
            $table->foreign('category_right_id')->references('id')->on('category_rights');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->boolean('display')->default(1)->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->bigInteger('action_id')->nullable()->unsigned()->comment('Id действия');
            $table->foreign('action_id')->references('id')->on('actions');

            $table->string('directive')->index()->comment('Директива (allow/deny)');
            $table->string('alias_right')->index()->comment('Полный алиас права');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->timestamps();
            $table->boolean('moderation')->default(0)->comment('Модерация');
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
        Schema::dropIfExists('rights');
    }
}
