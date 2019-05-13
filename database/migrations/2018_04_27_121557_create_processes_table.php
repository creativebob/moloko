<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->comment('Имя процесса');
            $table->text('description')->nullable()->comment('Описание процесса');

            $table->bigInteger('processes_group_id')->nullable()->unsigned()->comment('Id группы процесса');
            $table->foreign('processes_group_id')->references('id')->on('processes_groups');

            $table->bigInteger('processes_type_id')->nullable()->unsigned()->comment('Id типа процесса');
            $table->foreign('processes_type_id')->references('id')->on('processes_types');

            $table->string('internal')->nullable()->comment('Имя генерируемого процесса');
            $table->string('manually')->nullable()->comment('Имя для поиска (руками)');
            $table->string('external')->nullable()->comment('Имя внешнего процесса');

            $table->bigInteger('manufacturer_id')->nullable()->unsigned()->comment('Id производителя процесса');
            $table->foreign('manufacturer_id')->references('id')->on('companies');

            $table->integer('cost_default')->nullable()->comment('Фиксированная себестоимость (руками)');
            $table->integer('cost_mode')->nullable()->unsigned()->comment('Режим определения себестоимости');

            $table->integer('price_default')->nullable()->comment('Фиксированная цена (руками)');
            $table->integer('price_mode')->nullable()->unsigned()->comment('Режим определения цены');
            
            $table->bigInteger('price_rule_id')->nullable()->unsigned()->comment('ID ценовой политики');
            $table->foreign('price_rule_id')->references('id')->on('price_rules');

            $table->bigInteger('album_id')->nullable()->unsigned()->comment('ID альбома');
            $table->foreign('album_id')->references('id')->on('albums');

            $table->bigInteger('photo_id')->nullable()->unsigned()->comment('ID аватара');
            $table->foreign('photo_id')->references('id')->on('photos');

            // $table->boolean('portion_status')->default(0)->unsigned()->comment('Статус порции');
            // $table->string('portion_name')->nullable()->comment('Имя порции');
            // $table->string('portion_abbreviation')->nullable()->comment('Сокращение порции');
            // $table->integer('portion_count')->nullable()->unsigned()->comment('Количество в порции');

            // $table->integer('metrics_count')->nullable()->unsigned()->index()->comment('Количество метрик у артикула');
            // $table->integer('compositions_count')->nullable()->unsigned()->index()->comment('Количество составов у артикула');

            $table->boolean('draft')->default(0)->unsigned()->comment('Статус черновика');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');

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
        Schema::dropIfExists('processes');
    }
}
