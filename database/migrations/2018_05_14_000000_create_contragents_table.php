<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContragentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contragents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('contragent_id')->nullable()->unsigned()->comment('ID контрагента');
            $table->foreign('contragent_id')->references('id')->on('companies');

            $table->integer('client_status')->nullable()->unsigned()->comment('Статус клиента (Null или 1)');
            $table->integer('vendor_status')->nullable()->unsigned()->comment('Статус поставщика (Null или 1)');
            $table->integer('manufacturer_status')->nullable()->unsigned()->comment('Статус производителя (Null или 1)');
            $table->integer('dealer_status')->nullable()->unsigned()->comment('Статус дилера (Null или 1)');


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
            // $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contragents');
    }
}
