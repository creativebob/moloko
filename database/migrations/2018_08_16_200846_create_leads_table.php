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
            $table->bigIncrements('id');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');

            $table->string('name', 80)->nullable()->index()->comment('Имя лида');
            $table->text('description')->nullable()->comment('Описание для лида');

            $table->decimal('badget', 12, 2)->default(0)->comment('Предполагаемая сумма сделки');
            $table->decimal('payment', 12, 4)->default(0)->comment('Оплата по лиду');

            $table->decimal('order_amount_base', 12, 4)->default(0)->comment('Сумма первоначального заказа');

            $table->string('company_name', 80)->nullable()->index()->comment('Имя компании лида');

            $table->string('case_number', 20)->nullable()->index()->comment('Номер обращения по правилам компании');
            $table->integer('serial_number')->unsigned()->nullable()->comment('Дневной серийный номер лида по менеджеру');

            $table->boolean('private_status')->default(0)->comment('Компания или физическое лицо');

            $table->bigInteger('phone')->nullable()->comment('Телефон лида');
            $table->bigInteger('extra_phone')->nullable()->comment('Дополнительный телефон');
            $table->string('email')->nullable()->comment('Почта');

            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Адрес объекта');
            $table->foreign('location_id')->references('id')->on('locations');

            $table->bigInteger('site_id')->nullable()->unsigned()->comment('Сайт');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->bigInteger('source_id')->nullable()->unsigned()->comment('Источник лида');
            $table->foreign('source_id')->references('id')->on('sources');

            $table->bigInteger('medium_id')->nullable()->unsigned()->comment('Тип трафика');
            $table->foreign('medium_id')->references('id')->on('mediums');

            $table->bigInteger('campaign_id')->nullable()->unsigned()->comment('Рекламная кампания');
            $table->foreign('campaign_id')->references('id')->on('campaigns');

            $table->string('utm_source')->nullable()->comment('Источник');
            $table->string('utm_campaign')->nullable()->comment('Рекламная кампания');
            $table->string('utm_medium')->nullable()->comment('Тип');
            $table->string('utm_content')->nullable()->comment('Рекламное объявление');
            $table->string('utm_term')->nullable()->comment('Ключевая фраза');

            $table->string('promocode', 30)->nullable()->comment('Промокод');

            $table->bigInteger('sector_id')->nullable()->unsigned()->comment('Сектор');
            $table->foreign('sector_id')->references('id')->on('sectors');

            $table->bigInteger('lead_type_id')->nullable()->unsigned()->default(1)->comment('Тип обращения');
            $table->foreign('lead_type_id')->references('id')->on('lead_types');

            $table->bigInteger('lead_method_id')->nullable()->unsigned()->default(1)->comment('Способ обращения');
            $table->foreign('lead_method_id')->references('id')->on('lead_methods');

            $table->bigInteger('manager_id')->nullable()->unsigned()->default(1)->comment('ID пользователя');
            $table->foreign('manager_id')->references('id')->on('users');

            $table->bigInteger('stage_id')->nullable()->unsigned()->comment('ID этапа');
            $table->foreign('stage_id')->references('id')->on('stages');

            $table->integer('challenges_active_count')->default(0)->unsigned()->comment('Кол-во активных задач');

            $table->integer('choice_id')->nullable()->unsigned()->default(null)->comment('ID сущности которая интересует');
            $table->string('choice_type')->nullable()->comment('Модель сущности');

            $table->bigInteger('user_id')->nullable()->unsigned()->comment('Пользователь');
            $table->foreign('user_id')->references('id')->on('users');

            $table->bigInteger('organization_id')->unsigned()->nullable()->comment('Id организации');
            $table->foreign('organization_id')->references('id')->on('companies');

            $table->bigInteger('client_id')->nullable()->unsigned()->comment('Клиент');
            $table->foreign('client_id')->references('id')->on('clients');

            // Старый id из другой базы
            $table->integer('old_lead_id')->nullable()->unsigned()->comment('ID из другой базы');

            $table->integer('old_case_number')->nullable()->unsigned()->comment('Номер обращения из другой базы');

            $table->boolean('draft')->default(1)->comment('Черновик');

	        $table->timestamp('shipment_at')->nullable()->comment('Дата отгрузки');

            $table->boolean('is_create_parse')->default(0)->comment('Создан парсером');
            $table->boolean('is_link_parse')->default(0)->comment('Связан парсером со сметой');

            $table->boolean('need_delivery')->default(0)->comment('Нужна доставка');


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
        Schema::dropIfExists('leads');
    }
}
