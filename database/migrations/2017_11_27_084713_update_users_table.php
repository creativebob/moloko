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

            // Переименуем имя столбца name из родной таблицы Laravel в имя login
            $table->renameColumn('name', 'login');

            $table->string('nickname', 20)->nullable()->index()->comment('Псевдоним')->after('password');
            $table->string('first_name', 20)->nullable()->index()->comment('Полное имя')->after('nickname');
            $table->string('second_name', 20)->nullable()->index()->comment('Фамилия')->after('first_name');
            $table->string('patronymic', 20)->nullable()->index()->comment('Отчество')->after('second_name');

            $table->integer('sex')->nullable()->unsigned()->comment('Пол')->after('patronymic');
            $table->date('birthday')->nullable()->comment('Дата рождения')->after('sex');

            $table->bigInteger('phone')->unique()->nullable()->comment('Телефон')->after('birthday');
            $table->bigInteger('extra_phone')->nullable()->comment('Дополнительный телефон')->after('phone');
            $table->bigInteger('telegram_id')->unsigned()->unique()->nullable()->comment('ID Telegram')->after('extra_phone');
            $table->integer('city_id')->nullable()->unsigned()->comment('Id города')->after('telegram_id');
            $table->string('address', 60)->nullable()->comment('Адрес')->after('city_id');

            $table->integer('orgform_status')->nullable()->comment('Компания 1 или частное лицо 0')->after('address');
            $table->string('company_name', 40)->nullable()->index()->comment('Имя компании')->after('orgform_status');
            $table->bigInteger('inn')->nullable()->unsigned()->comment('ИНН')->after('company_name');
            $table->bigInteger('kpp')->nullable()->unsigned()->comment('КПП')->after('inn');
            $table->bigInteger('account_settlement')->nullable()->unsigned()->comment('Расчетный счет')->after('kpp');
            $table->bigInteger('account_correspondent')->nullable()->unsigned()->comment('Корреспондентский счет')->after('account_settlement');
            $table->string('bank', 40)->nullable()->comment('Название банка')->after('account_correspondent');

            $table->string('passport_number')->nullable()->unique()->comment('Номер паспорта')->after('bank');
            $table->date('passport_date')->nullable()->comment('Дата выдачи паспорта')->after('passport_number');
            $table->string('passport_released', 60)->nullable()->comment('Кем выдан паспорт')->after('passport_date');
            $table->string('passport_address', 60)->nullable()->comment('Адрес прописки')->after('passport_released');

            $table->integer('contragent_status')->nullable()->unsigned()->comment('Сотрудник 1 или Клиент 0')->after('passport_address');
            $table->integer('lead_id')->nullable()->unsigned()->comment('Id лида')->after('contragent_status');
            // $table->foreign('lead_id')->references('lead_id')->on('leads');
            $table->integer('employee_id')->nullable()->unsigned()->comment('Id сотрудника')->after('lead_id');
            // $table->foreign('employee_id')->references('employee_id')->on('employees');
            $table->integer('access_block')->nullable()->unsigned()->comment('Доступ открыт 0 или Блокирован 1')->after('employee_id');

            $table->integer('group_users_id')->nullable()->unsigned()->comment('Группа доступа по функционалу')->after('access_block');
            $table->integer('group_filials_id')->nullable()->unsigned()->comment('Группа доступа по филиалу')->after('group_users_id');

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
            $table->renameColumn('login', 'name');
            $table->dropColumn('nickname');
            $table->dropColumn('first_name');
            $table->dropColumn('second_name');
            $table->dropColumn('patronymic');
            $table->dropColumn('sex');
            $table->dropColumn('birthday');

            $table->dropColumn('phone');
            $table->dropColumn('extra_phone');
            $table->dropColumn('telegram_id');
            $table->dropColumn('city_id');
            $table->dropColumn('address');

            $table->dropColumn('orgform_status');
            $table->dropColumn('company_name');
            $table->dropColumn('inn');
            $table->dropColumn('kpp');
            $table->dropColumn('account_settlement');
            $table->dropColumn('account_correspondent');
            $table->dropColumn('bank');
                        
            $table->dropColumn('passport_number');
            $table->dropColumn('passport_date');
            $table->dropColumn('passport_released');
            $table->dropColumn('passport_address');

            $table->dropColumn('contragent_status');
            $table->dropColumn('lead_id');
            $table->dropColumn('employee_id');
            $table->dropColumn('access_block');

            $table->dropColumn('group_users_id');
            $table->dropColumn('group_filials_id');          

        });

    }
}
