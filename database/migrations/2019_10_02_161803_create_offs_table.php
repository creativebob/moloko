<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('stock_id')->unsigned()->nullable()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->morphs('document');
            $table->morphs('documents_item');

            $table->morphs('cmv');

            $table->morphs('storage');

	        $table->decimal('count', 12,4)->default(0)->comment('Количество');

            $table->decimal('weight_unit', 9, 4)->default(0)->comment('Вес за единицу (кг)');
            $table->decimal('volume_unit', 15, 8)->default(0)->comment('Обьем за единицу (м3)');

            $table->decimal('cost_unit', 16, 8)->default(0)->comment('Стоимость за единицу');
	        $table->decimal('total', 16, 8)->default(0)->comment('Сумма');


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
        Schema::dropIfExists('offs');
    }
}
