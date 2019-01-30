<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('report_operations', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('reports_id');
			$table->integer('accounts_id');
			$table->integer('type');
			$table->string('shortcode');
			$table->float('summa');
			$table->string('comment')->nullable();
			$table->datetime('datetime');
            $table->timestamps();
			
			$table->foreign('reports_id')->references('id')->on('reports')
                ->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('accounts_id')->references('id')->on('accounts')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('report')->dropIfExists('report_operations');
    }
}
