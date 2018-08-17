<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->string('name', 20)->nullable()->index()->comment('Имя лида');
            $table->text('description')->nullable()->comment('Описание для лида');

            $table->integer('badget')->nullable()->unsigned()->comment('Предполагаемая сумма сделки');
            $table->string('case_number', 20)->nullable()->index()->comment('Номер обращения по правилам компании');

            $table->bigInteger('phone')->unique()->nullable()->comment('Телефон компании');
            $table->bigInteger('extra_phone')->nullable()->comment('Дополнительный телефон');
            $table->string('email')->nullable()->comment('Почта');

            $table->integer('location_id')->nullable()->unsigned()->comment('Адрес объекта');
            $table->foreign('location_id')->references('id')->on('locations');

            $table->integer('site_id')->nullable()->unsigned()->comment('Сайт');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->integer('sourse_id')->nullable()->unsigned()->comment('Источник лида');
            $table->foreign('sourse_id')->references('id')->on('sources');

            $table->integer('medium_id')->nullable()->unsigned()->comment('Тип трафика');
            $table->foreign('medium_id')->references('id')->on('mediums');

            $table->integer('campaign_id')->nullable()->unsigned()->comment('Рекламная кампания');
            $table->foreign('campaign_id')->references('id')->on('campaigns');

            $table->string('utm_content')->nullable()->comment('Рекламное объявление, ссылка');
            $table->string('utm_term')->nullable()->comment('Ключевая фраза');

            $table->integer('sector_id')->nullable()->unsigned()->comment('Сектор');
            $table->foreign('sector_id')->references('id')->on('sectors');

            $table->integer('lead_type_id')->nullable()->unsigned()->comment('Тип обращения');
            $table->foreign('lead_type_id')->references('id')->on('lead_types');

            $table->integer('manager_id')->nullable()->unsigned()->comment('ID пользователя');
            $table->foreign('manager_id')->references('id')->on('users');

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
