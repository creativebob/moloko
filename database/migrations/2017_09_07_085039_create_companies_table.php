<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{

    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable()->index()->comment('Имя компании');
            $table->string('alias', 40)->unique()->nullable()->index()->comment('Алиас компании');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('email')->nullable()->comment('Почта');

            $table->integer('location_id')->nullable()->unsigned()->comment('Адрес компании');
            $table->foreign('location_id')->references('id')->on('locations');

            $table->integer('legal_form_id')->default(1)->unsigned()->comment('Правовая форма');
            $table->foreign('legal_form_id')->references('id')->on('legal_forms');

            $table->bigInteger('inn')->nullable()->unsigned()->comment('ИНН компании');
            $table->bigInteger('kpp')->nullable()->unsigned()->comment('КПП');

            $table->bigInteger('ogrn')->nullable()->unsigned()->comment('Основной государственный регистрационный номер');
            $table->bigInteger('okpo')->nullable()->unsigned()->comment('Общероссийский классификатор предприятий и организаций');
            $table->string('okved')->nullable() ->comment('Общероссийский классификатор видов экономической деятельности');

            $table->integer('bic') -> length (9)->nullable()->unsigned()->comment('Банковский идентификационный код');

            $table->integer('director_user_id')->nullable()->unsigned()->comment('Директор компании');
            $table->foreign('director_user_id')->references('id')->on('users');

            $table->integer('admin_user_id')->nullable()->unsigned()->comment('Администратор компании');
            $table->foreign('admin_user_id')->references('id')->on('users');

            $table->integer('schedule_id')->nullable()->unsigned()->comment('Id графика работы');
            $table->foreign('schedule_id')->references('id')->on('schedules');

            $table->integer('sector_id')->nullable()->unsigned()->comment('Id сектора');
            $table->foreign('sector_id')->references('id')->on('sectors');

            $table->boolean('external_control')->default(0)->comment('Внешнее управление');

            $table->text('about')->nullable()->comment('Информация о компании');
            $table->text('seo_description')->nullable()->comment('Описание для сайта');

            // Общие настройки
            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
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
