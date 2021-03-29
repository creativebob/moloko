<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('shift_id')->unsigned()->nullable()->comment('Id смены')->after('currency_id');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->decimal('paid', 10, 2)->default(0)->comment('Оплачено всего')->after('points');
            $table->decimal('debit', 10, 2)->default(0)->comment('Долг')->after('paid');
            $table->string('payment_type')->nullable()->comment('Тип платежей')->after('debit');

            $table->string('is_need_parse')->default(1)->comment('Нужно парсить')->after('is_create_parse');
        });

//        Schema::table('staff', function (Blueprint $table) {
//            $table->dropColumn('archive');
//            $table->timestamp('archived_at')->nullable()->comment('Архив')->after('rate');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('shift_id');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn([
                'paid',
                'debit',
                'payment_type',
                'is_need_parse',
            ]);
        });

//        Schema::table('staff', function (Blueprint $table) {
//            $table->dropColumn('archived_at');
//            $table->boolean('archive')->default(0)->unsigned()->comment('Архив')->after('rate');
//        });
    }
}
