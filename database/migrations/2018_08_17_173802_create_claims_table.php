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
            $table->bigIncrements('id');

            // $table->string('name')->index()->comment('Название этапа');
            $table->text('body')->nullable()->comment('Описание рекламации');

            $table->string('case_number')->nullable()->index()->comment('Номер рекламации');
            $table->string('source_lead_id')->nullable()->index()->comment('Номер обращения по которму была заведена рекламация');

            $table->integer('serial_number')->unsigned()->nullable()->comment('Дневной серийный номер рекламации на компанию');

            $table->bigInteger('lead_id')->nullable()->unsigned()->comment('ID лида на котором рекламация');
            $table->foreign('lead_id')->references('id')->on('leads');

            $table->bigInteger('manager_id')->nullable()->unsigned()->comment('ID менеджера');
            $table->foreign('manager_id')->references('id')->on('users');

            $table->integer('status')->nullable()->unsigned()->comment('Статус выполнения (1 - не выполнено)');

            $table->integer('old_claim_id')->nullable()->unsigned()->comment('ID рекламации из старой базы');


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
        Schema::dropIfExists('claims');
    }
}
