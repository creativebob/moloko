<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->nullable()->comment('Имя артикула');
            $table->text('description')->nullable()->comment('Описание артикула');

            $table->bigInteger('articles_group_id')->nullable()->unsigned()->comment('Id группы артикула');
            $table->foreign('articles_group_id')->references('id')->on('articles_groups');

            $table->string('internal')->nullable()->comment('Имя генерируемого артикула');
            $table->string('manually')->nullable()->comment('Имя для поиска (руками)');
            $table->string('external')->nullable()->comment('Имя внешнего артикула');

            $table->bigInteger('manufacturer_id')->nullable()->unsigned()->comment('Id производителя артикула');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');

            $table->boolean('kit')->default(0)->unsigned()->comment('Статус набора');

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

            $table->string('video_url')->nullable()->comment('Ссылка на видео');

            $table->boolean('portion_status')->default(0)->unsigned()->comment('Статус порции');
            $table->string('portion_name')->nullable()->comment('Имя порции');
            $table->string('portion_abbreviation')->nullable()->comment('Сокращение порции');
            $table->integer('portion_count')->nullable()->unsigned()->comment('Количество в порции');

            $table->decimal('weight', 15, 8)->nullable()->comment('Вес (кг)');

            $table->bigInteger('unit_weight_id')->nullable()->unsigned()->comment('Id единицы измерения для веса');
            $table->foreign('unit_weight_id')->references('id')->on('units');

            $table->decimal('volume', 15, 8)->nullable()->comment('Объем (к.м.)');

            $table->bigInteger('unit_volume_id')->nullable()->unsigned()->comment('Id единицы измерения для объема');
            $table->foreign('unit_volume_id')->references('id')->on('units');

            $table->bigInteger('unit_id')->nullable()->unsigned()->comment('Id единицы измерения');
            $table->foreign('unit_id')->references('id')->on('units');

            // $table->integer('metrics_count')->nullable()->unsigned()->index()->comment('Количество метрик у артикула');
            // $table->integer('compositions_count')->nullable()->unsigned()->index()->comment('Количество составов у артикула');

            $table->boolean('draft')->default(0)->unsigned()->comment('Статус черновика');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(0)->comment('Отображение на сайте');
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
        Schema::dropIfExists('articles');
    }
}
