<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');
            
            $table->string('name')->index()->comment('Название типа трафика (среды)');
            $table->text('description')->nullable()->comment('Description для типа трафика');
            $table->string('utm')->index()->comment('UTM метка: source_utm');

            $table->bigInteger('sources_id')->nullable()->unsigned()->comment('ID источника трафика');
            $table->foreign('sources_id')->references('id')->on('sources');

            $table->timestamp('begined_at')->nullable()->comment('Время начала');
            $table->timestamp('ended_at')->nullable()->comment('Время окончания');

            $table->integer('archive')->nullable()->unsigned()->comment('Перенесен в архив');
            $table->integer('external')->nullable()->unsigned()->comment('Внешний ID источника');


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
        Schema::dropIfExists('campaigns');
    }
}
