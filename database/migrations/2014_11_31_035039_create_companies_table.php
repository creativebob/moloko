<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{

    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->index()->comment('Имя компании');
            $table->string('name_short')->nullable()->index()->comment('Короткое имя компании');
            $table->string('prename')->nullable()->index()->comment('Статус компании');
            $table->string('slogan')->nullable()->comment('Слоган');
            $table->string('designation')->nullable()->index()->comment('Коммерческое обозначение');

            $table->string('alias', 120)->unique()->nullable()->index()->comment('Алиас компании');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('email')->nullable()->comment('Почта');

            $table->string('inn', 12)->nullable()->comment('ИНН');
            $table->string('kpp', 9)->nullable()->comment('КПП');

            $table->string('ogrn', 15)->nullable()->comment('Основной государственный регистрационный номер');
            $table->string('okpo', 10)->nullable()->comment('Общероссийский классификатор предприятий и организаций');
            $table->string('okved', 8)->nullable() ->comment('Общероссийский классификатор видов экономической деятельности');
            $table->string('bic', 9)->nullable()->comment('Банковский идентификационный код');

            $table->boolean('external_control')->default(0)->comment('Внешнее управление');

            $table->text('about')->nullable()->comment('Информация о компании');
            $table->text('seo_description')->nullable()->comment('Описание для сайта');

            $table->date('foundation_date')->nullable()->comment('Дата основания');

            $table->bigInteger('external_id')->nullable()->unsigned()->comment('Внешний id компании');


            // Общие настройки
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

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
