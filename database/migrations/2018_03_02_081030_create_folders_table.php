<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoldersTable extends Migration
{

    public function up()
    {

        Schema::create('folders', function (Blueprint $table) {

            $table->increments('id');
            $table->string('folder_name')->nullable()->comment('Имя папки');
            $table->string('folder_alias')->nullable()->comment('Алиас');

            $table->string('folder_url')->nullable()->comment('URL папки');

            $table->integer('folder_parent_id')->unsigned()->nullable()->comment('Id родителя папки');
            $table->foreign('folder_parent_id')->references('id')->on('folders');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('filial_id')->nullable()->unsigned()->comment('ID филиала компании');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->timestamps();
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
            $table->softDeletes();
        });

    }

    public function down()
    {
        Schema::dropIfExists('folders');
    }
}
