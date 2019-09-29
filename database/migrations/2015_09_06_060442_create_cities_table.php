<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название населенного пункта');
            $table->string('alias')->nullable()->index()->comment('Алиас населенного пункта');

            $table->bigInteger('area_id')->unsigned()->nullable()->comment('Район населенного пункта');
            $table->foreign('area_id')->references('id')->on('areas');

            $table->bigInteger('region_id')->unsigned()->nullable()->comment('Область населенного пункта');
            $table->foreign('region_id')->references('id')->on('regions');
            
            $table->bigInteger('country_id')->nullable()->unsigned()->comment('Id страны');
            $table->foreign('country_id')->references('id')->on('countries');
            
            $table->integer('code')->unsigned()->nullable()->comment('Код населенного пункта');
            $table->integer('vk_external_id')->unique()->unsigned()->nullable()->comment('Внешний Id (из базы vk)');


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
        Schema::dropIfExists('cities');
    }
}
