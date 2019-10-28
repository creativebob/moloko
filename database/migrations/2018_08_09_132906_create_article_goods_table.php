<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_goods', function (Blueprint $table) {
            $table->bigInteger('article_id')->nullable()->unsigned()->comment('Id артикула');
            $table->foreign('article_id')->references('id')->on('articles');

            // $table->morphs('composition');

            $table->bigInteger('goods_id')->nullable()->unsigned()->comment('Id товара');
            $table->foreign('goods_id')->references('id')->on('goods');

            $table->decimal('value', 12, 2)->default(0)->comment('Значение');
            $table->decimal('use', 12, 2)->default(0)->comment('Использование');
            $table->decimal('leftover', 12, 2)->default(0)->comment('Остаток');
            $table->decimal('waste', 12, 2)->default(0)->comment('Отходы');

            $table->bigInteger('leftover_operation_id')->nullable()->unsigned()->comment('Id операции над остатком');
            $table->foreign('leftover_operation_id')->references('id')->on('leftover_operations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_goods');
    }
}
