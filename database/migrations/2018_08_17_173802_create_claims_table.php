<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('company_id')->nullable()->unsigned()->comment('ID компании');
            $table->foreign('company_id')->references('id')->on('companies');
            
            // $table->string('name')->index()->comment('Название этапа');
            $table->text('body')->nullable()->comment('Описание рекламации');

            $table->string('case_number')->nullable()->index()->comment('Номер рекламации');

            $table->integer('lead_id')->nullable()->unsigned()->comment('ID лида');
            $table->foreign('lead_id')->references('id')->on('leads');

            $table->integer('manager_id')->nullable()->unsigned()->comment('ID лида');
            $table->foreign('manager_id')->references('id')->on('users');

            $table->integer('old_claim_id')->nullable()->unsigned()->comment('ID рекламации из старой базы');

            $table->integer('display')->nullable()->unsigned()->comment('Отображение на сайте');

            $table->integer('sort')->nullable()->unsigned()->index()->comment('Поле для сортировки');

            $table->integer('author_id')->nullable()->unsigned()->comment('Id создателя записи');
            $table->foreign('author_id')->references('id')->on('users');

            $table->integer('editor_id')->nullable()->unsigned()->comment('Id редактора записи');
            $table->integer('system_item')->nullable()->unsigned()->comment('Флаг системной записи: 1 или null');

            $table->timestamps();
            $table->integer('moderation')->nullable()->unsigned()->comment('На модерации');
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
        Schema::dropIfExists('claims');
    }
}
