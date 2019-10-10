<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsignmentsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignments_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('consignment_id')->unsigned()->nullable()->comment('Id накладной');
            $table->foreign('consignment_id')->references('id')->on('consignments');

            $table->morphs('cmv');
	
	        $table->decimal('count', 12,4)->default(0)->comment('Количество');
            $table->decimal('cost', 12, 4)->default(0)->comment('Цена за единицу');
            $table->decimal('amount', 16, 8)->default(0)->comment('Сумма');

            $table->integer('vat_rate')->nullable()->comment('Размер налога НДС');
            $table->integer('amount_vat')->nullable()->comment('Сумма НДС');

            $table->integer('total')->default(0)->comment('Итого - Сумма с учетом НДС');

            $table->text('description')->nullable()->comment('Комментарий к позиции');

            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('ID склада на который приходовать ТМЦ');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('entity_id')->nullable()->unsigned()->comment('Id сущности ТМЦ');
            $table->foreign('entity_id')->references('id')->on('entities');

            
            // Общие настройки
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
        Schema::dropIfExists('consignments_items');
    }
}
