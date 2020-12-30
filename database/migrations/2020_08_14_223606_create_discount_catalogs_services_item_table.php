<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountCatalogsServicesItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_catalogs_services_item', function (Blueprint $table) {
            $table->bigInteger('catalogs_services_item_id')->unsigned()->comment('Id раздела услуги');
//            $table->foreign('catalogs_services_item_id')->references('id')->on('catalogs_services_items');

            $table->bigInteger('discount_id')->unsigned()->comment('Id скидки');
//            $table->foreign('discount_id')->references('id')->on('discounts');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_catalogs_services_item');
    }
}
