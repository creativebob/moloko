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
            $table->unique('login');

            $table->string('nickname', 20)->nullable()->index()->comment('Псевдоним')->after('password');
            $table->string('first_name', 20)->nullable()->index()->comment('Полное имя')->after('nickname');
            $table->string('second_name', 20)->nullable()->index()->comment('Фамилия')->after('first_name');
            $table->string('patronymic', 20)->nullable()->index()->comment('Отчество')->after('second_name');

            $table->integer('sex')->unsigned()->comment('Пол')->default(1)->after('patronymic');
            $table->date('birthday')->nullable()->comment('Дата рождения')->after('sex');

            $table->bigInteger('phone')->unique()->nullable()->comment('Телефон')->after('birthday');
            $table->bigInteger('extra_phone')->nullable()->comment('Дополнительный телефон')->after('phone');
            $table->bigInteger('telegram')->unsigned()->unique()->nullable()->comment('Telegram')->after('extra_phone');

            $table->integer('location_id')->nullable()->unsigned()->comment('Адрес пользователя')->after('telegram_id');

            $table->string('photo')->nullable()->comment('Фото')->after('location_id');
             $table->integer('photo_id')->nullable()->unsigned()->comment('Id аватарки')->after('photo');

            $table->integer('orgform_status')->nullable()->comment('Представляет компанию 1 или частное лицо 0')->after('photo_id');
            $table->bigInteger('user_inn')->nullable()->unsigned()->comment('ИНН')->after('orgform_status');

            $table->string('passport_number')->nullable()->unique()->comment('Номер паспорта')->after('user_inn');
            $table->date('passport_date')->nullable()->comment('Дата выдачи паспорта')->after('passport_number');
            $table->string('passport_released', 60)->nullable()->comment('Кем выдан паспорт')->after('passport_date');
            $table->string('passport_address', 60)->nullable()->comment('Адрес прописки')->after('passport_released');

            $table->text('about')->nullable()->comment('Информация о пользователе')->after('passport_address');
            $table->text('specialty')->nullable()->comment('Специальность')->after('about');
            $table->text('degree')->nullable()->comment('Ученая степень, звание')->after('specialty');
            $table->text('quote')->nullable()->comment('Цитата, высказывание, фраза')->after('degree');

            $table->string('liter')->nullable()->unique()->comment('Литера')->after('quote');   

            $table->boolean('user_type')->default(0)->comment('Свой 1 или Чужой 0')->after('liter');
            $table->boolean('access_block')->default(1)->comment('Доступ открыт 0 или Блокирован 1')->default('0')->after('user_type');

            $table->integer('company_id')->nullable()->unsigned()->comment('Компания пользователя')->after('access_block');
            $table->integer('filial_id')->nullable()->unsigned()->comment('ID филиала компании')->after('company_id');

            $table->integer('god')->nullable()->unsigned()->comment('Божественное право')->default(null)->after('company_id');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->softDeletes();

        });

        Schema::table('users', function(Blueprint $table) {
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
            $table->dropUnique('users_login_unique');
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
            $table->dropColumn('god');
            $table->dropColumn('moderation');  
            // $table->dropColumn('sort');        

            $table->dropForeign('users_location_id_foreign');
            $table->dropForeign('users_company_id_foreign');
            $table->dropForeign('users_author_id_foreign');
            $table->dropForeign('users_filial_id_foreign');  


        });

    }
}
