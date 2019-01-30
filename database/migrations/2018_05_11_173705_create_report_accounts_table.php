<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('report_accounts', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('report_id');
			$table->integer('account_id');
			$table->float('summa_opened',2);
			$table->float('summa_closed',2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('report')->dropIfExists('report_accounts');
    }
}
