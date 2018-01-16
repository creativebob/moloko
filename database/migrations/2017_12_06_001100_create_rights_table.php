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
            $table->increments('id');
            $table->string('right_name')->index()->comment('Простое имя правила для вывода');

            $table->string('object_entity')->index()->comment('ID сущности (Полиморфно)');

            $table->integer('category_right_id')->nullable()->unsigned()->comment('Категория правила');
            $table->foreign('category_right_id')->references('id')->on('category_rights');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->string('directive')->index()->comment('Директива (allow/deny)');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->timestamps();
            $table->dateTime('moderated_at')->nullable()->comment('Дата модерации');
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
