<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {

            $table->string('nickname', 80)->nullable()->index()->comment('Псевдоним')->after('password');
            $table->string('first_name', 20)->nullable()->index()->comment('Полное имя')->after('nickname');
            $table->string('second_name', 20)->nullable()->index()->comment('Фамилия')->after('first_name');
            $table->string('patronymic', 20)->nullable()->index()->comment('Отчество')->after('second_name');

            $table->string('name')->nullable()->index()->comment('Имя')->after('patronymic');

            $table->integer('sex')->unsigned()->comment('Пол')->default(1)->after('name');
            $table->date('birthday_date')->nullable()->comment('Дата рождения')->after('sex');

            $table->bigInteger('phone')->unique()->nullable()->comment('Телефон')->after('birthday_date');
            $table->bigInteger('extra_phone')->nullable()->comment('Дополнительный телефон')->after('phone');
            $table->bigInteger('telegram')->unsigned()->unique()->nullable()->comment('Telegram')->after('extra_phone');

            $table->bigInteger('location_id')->nullable()->unsigned()->comment('Адрес пользователя')->after('telegram');

            $table->integer('photo_id')->nullable()->unsigned()->comment('Id аватарки')->after('location_id');

            $table->integer('orgform_status')->nullable()->comment('Представляет компанию 1 или частное лицо 0')->after('photo_id');
            $table->bigInteger('user_inn')->nullable()->unsigned()->comment('ИНН')->after('orgform_status');

            $table->string('passport_number')->nullable()->unique()->comment('Номер паспорта')->after('user_inn');
            $table->date('passport_date')->nullable()->comment('Дата выдачи паспорта')->after('passport_number');
            $table->string('passport_released', 200)->nullable()->comment('Кем выдан паспорт')->after('passport_date');
            $table->string('passport_address', 250)->nullable()->comment('Адрес прописки')->after('passport_released');

            $table->text('about')->nullable()->comment('Информация о пользователе')->after('passport_address');
            $table->text('specialty')->nullable()->comment('Специальность')->after('about');
            $table->text('degree')->nullable()->comment('Ученая степень, звание')->after('specialty');
            $table->text('quote')->nullable()->comment('Цитата, высказывание, фраза')->after('degree');

            $table->string('liter')->nullable()->unique()->comment('Литера')->after('quote');

            $table->boolean('user_type')->default(0)->comment('Свой 1 или Чужой 0')->after('liter');
            $table->boolean('access_block')->default(1)->comment('Доступ открыт 0 или Блокирован 1')->default('0')->after('user_type');

            $table->integer('access_code')->nullable()->unsigned()->comment('Код доступа')->after('access_block');

            $table->bigInteger('site_id')->nullable()->unsigned()->comment('Id сайта')->after('access_code');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->bigInteger('company_id')->nullable()->unsigned()->comment('Компания пользователя')->after('site_id');
            $table->bigInteger('filial_id')->nullable()->unsigned()->comment('ID филиала компании')->after('company_id');

            $table->integer('god')->nullable()->unsigned()->comment('Божественное право')->default(null)->after('company_id');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->softDeletes();

            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('filial_id')->references('id')->on('departments');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nickname');
            $table->dropColumn('first_name');
            $table->dropColumn('second_name');
            $table->dropColumn('patronymic');
            $table->dropColumn('sex');
            $table->dropColumn('birthday_date');

            $table->dropColumn('phone');
            $table->dropColumn('extra_phone');
            $table->dropColumn('telegram_id');

            $table->dropColumn('photo');
            $table->dropColumn('photo_id');

            $table->dropColumn('orgform_status');
            $table->dropColumn('user_inn');

            $table->dropColumn('passport_number');
            $table->dropColumn('passport_date');
            $table->dropColumn('passport_released');
            $table->dropColumn('passport_address');

            $table->dropColumn('user_type');
            $table->dropColumn('access_block');
            $table->dropColumn('access_code');
            $table->dropColumn('god');
            $table->dropColumn('moderation');
            // $table->dropColumn('sort');

            $table->dropColumn('site_id');
            $table->dropForeign('users_site_id_foreign');

            $table->dropForeign('users_location_id_foreign');
            $table->dropForeign('users_company_id_foreign');
            $table->dropForeign('users_author_id_foreign');
            $table->dropForeign('users_filial_id_foreign');


        });

    }
}
