<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->index()->comment('Название');
            $table->text('description')->nullable()->comment('Описание');

            $table->decimal('percent', 10,2)->default(0)->comment('Процент скидки');
            $table->decimal('currency', 10,2)->default(0)->comment('Сумма скидки');

            $table->tinyInteger('mode')->unsigned()->default(1)->comment('Режим скидки: 1 - проценты, 2 - валюта');

            $table->boolean('is_conditions')->unsigned()->default(0)->comment('0 - без условий, 1 -  с условиями');

            $table->bigInteger('entity_id')->unsigned()->nullable()->comment('Id сущности');
//            $table->foreign('entity_id')->references('id')->on('entities');

            $table->boolean('is_block')->default(0)->unsigned()->comment('Блокировка дальнейших вычислений');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id филиала');
//            $table->foreign('filial_id')->references('id')->on('departments');

            $table->timestamp('begined_at')->nullable()->comment('Время начала');
            $table->timestamp('ended_at')->nullable()->comment('Время окончания');

            $table->boolean('archive')->default(0)->unsigned()->comment('Архив');
            $table->boolean('is_actual')->default(0)->comment('Актуальность');

            // Общие настройки
            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
//            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
            $table->boolean('display')->default(1)->comment('Отображение на сайте');
            $table->boolean('system')->default(0)->comment('Системная запись');
            $table->boolean('moderation')->default(0)->comment('Модерация');

            $table->bigInteger('author_id')->nullable()->unsigned()->comment('Id создателя записи');
//            $table->foreign('author_id')->references('id')->on('users');

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
        Schema::dropIfExists('discounts');
    }
}
