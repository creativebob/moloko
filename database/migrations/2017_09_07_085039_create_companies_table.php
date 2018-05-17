<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 40)->nullable()->index()->comment('Имя компании');
            $table->string('alias', 40)->unique()->nullable()->index()->comment('Алиас компании');

            $table->bigInteger('phone')->unique()->nullable()->comment('Телефон компании');
            $table->bigInteger('extra_phone')->nullable()->comment('Дополнительный телефон');

            $table->string('email')->nullable()->comment('Почта');

            // $table->integer('city_id')->nullable()->unsigned()->comment('Id города');
            // $table->foreign('city_id')->references('id')->on('cities');
            
            $table->integer('location_id')->nullable()->unsigned()->comment('Адрес компании');
            $table->foreign('location_id')->references('id')->on('locations');

            $table->bigInteger('inn')->nullable()->unsigned()->comment('ИНН компании');
            $table->bigInteger('kpp')->nullable()->unsigned()->comment('КПП');
            $table->string('account_settlement', 20)->nullable()->comment('Расчетный счет');
            $table->string('account_correspondent', 20)->nullable()->comment('Корреспондентский счет');
            $table->string('bank', 60)->nullable()->comment('Название банка');

            $table->integer('director_user_id')->nullable()->unsigned()->comment('Директор компании');
            // $table->foreign('user_id')->references('id')->on('users');

            $table->integer('admin_user_id')->nullable()->unsigned()->comment('Администратор компании');
            // $table->foreign('user_id')->references('id')->on('users');

            $table->integer('manufacturer_status')->nullable()->unsigned()->comment('Статус производителя');

            $table->integer('schedule_id')->nullable()->unsigned()->comment('Id графика работы');
            $table->foreign('schedule_id')->references('id')->on('schedules');

            $table->integer('sector_id')->nullable()->unsigned()->comment('Id сектора');
            $table->foreign('sector_id')->references('id')->on('sectors');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            // $table->foreign('author_id')->references('id')->on('users');

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
        Schema::dropIfExists('companies');
    }
}
