<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawsStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raws_stocks', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->bigInteger('company_id')->unsigned()->nullable()->comment('Id компании');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->bigInteger('filial_id')->unsigned()->nullable()->comment('Id отдела');
            $table->foreign('filial_id')->references('id')->on('departments');
            
            $table->bigInteger('stock_id')->nullable()->unsigned()->comment('Id склада');
            $table->foreign('stock_id')->references('id')->on('stocks');

            $table->bigInteger('cmv_id')->nullable()->unsigned()->comment('Id сырья');
            $table->foreign('cmv_id')->references('id')->on('raws');

            $table->integer('count')->default(0)->comment('Количество');
            
            $table->decimal('weight', 9, 4)->default(0)->comment('Вес (кг)');
	        $table->decimal('volume', 15, 8)->default(0)->comment('Обьем (м3)');
	        
            $table->string('serial')->nullable()->comment('Серийный номер');

            $table->bigInteger('manufacturer_id')->nullable()->unsigned()->comment('Id производителя');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');
	
	
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
        Schema::dropIfExists('raws_stocks');
    }
}
