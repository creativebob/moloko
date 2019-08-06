<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services_articles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('services_product_id')->nullable()->unsigned()->comment('ID товара');
            $table->foreign('services_product_id')->references('id')->on('services_products');

            $table->string('name')->nullable()->comment('Имя артикула');

            $table->text('description')->nullable()->comment('Описание артикула услуги');

            $table->string('internal')->nullable()->comment('Имя генерируемого артикула');

            $table->integer('metrics_count')->nullable()->unsigned()->index()->comment('Количество метрик у артикула');
            $table->integer('compositions_count')->nullable()->unsigned()->index()->comment('Количество состава у артикула');

            $table->integer('archive')->nullable()->unsigned()->comment('Статус архива');


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
        Schema::dropIfExists('services_articles');
    }
}
