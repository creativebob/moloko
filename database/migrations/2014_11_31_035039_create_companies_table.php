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
            $table->string('prename')->nullable()->index()->comment('Статус компании');
            $table->string('designation')->nullable()->index()->comment('Коммерческое обозначение');

            $table->string('alias', 40)->unique()->nullable()->index()->comment('Алиас компании');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('email')->nullable()->comment('Почта');

            $table->bigInteger('inn')->nullable()->unsigned()->comment('ИНН компании');
            $table->bigInteger('kpp')->nullable()->unsigned()->comment('КПП');

            $table->bigInteger('ogrn')->nullable()->unsigned()->comment('Основной государственный регистрационный номер');
            $table->bigInteger('okpo')->nullable()->unsigned()->comment('Общероссийский классификатор предприятий и организаций');
            $table->string('okved')->nullable() ->comment('Общероссийский классификатор видов экономической деятельности');

            $table->integer('bic') -> length (9)->nullable()->unsigned()->comment('Банковский идентификационный код');

            // Видимо устарело - на удаление!
            // $table->bigInteger('director_user_id')->nullable()->unsigned()->comment('Директор компании');
            // $table->foreign('director_user_id')->references('id')->on('users');

            // $table->bigInteger('admin_user_id')->nullable()->unsigned()->comment('Администратор компании');
            // $table->foreign('admin_user_id')->references('id')->on('users');

            $table->boolean('external_control')->default(0)->comment('Внешнее управление');

            $table->text('about')->nullable()->comment('Информация о компании');
            $table->text('seo_description')->nullable()->comment('Описание для сайта');
            $table->date('birthday_company')->nullable()->comment('Дата рождения компании');

            $table->bigInteger('external_id')->nullable()->unsigned()->comment('Внешний id компании');
            

            // Общие настройки
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(0)->comment('Отображение на сайте');
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
