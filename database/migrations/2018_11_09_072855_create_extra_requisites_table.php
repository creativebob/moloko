.<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExtraRequisitesTable extends Migration
{

    public function up()
    {
        Schema::create('extra_requisites', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('name')->nullable()->index()->comment('Название дополнительного реквизита');
            $table->text('description')->nullable()->comment('Описание');

            $table->string('alias')->nullable()->index()->comment('Алиас реквизита');


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
            // $table->softDeletes();

        });
    }

    public function down()
    {
        Schema::dropIfExists('extra_requisites');
    }
}
