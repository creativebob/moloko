<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdersTable extends Migration
{

    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->integer('amount')->nullable()->unsigned()->comment('Сумма по заказу');
            $table->integer('payment')->nullable()->unsigned()->comment('Оплачено');
            $table->integer('client_id')->nullable()->unsigned()->comment('ID клиента');

        });

        // Добавляем поля со связями
        Schema::table('orders', function(Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients');
        });

    }

    public function down()
    {

        Schema::table('orders', function (Blueprint $table) {
            
            $table->dropColumn('amount');
            $table->dropColumn('payment');

            // Удаляем поля со связями
            $table->dropForeign('orders_client_id_foreign');

        });
    }
}
