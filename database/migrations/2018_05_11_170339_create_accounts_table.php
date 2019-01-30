<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('accounts', function (Blueprint $table) {
            $table->increments('id');
			$table->string('title');
			$table->string('shortcode');
            $table->timestamps();
			
			$table->foreign('shortcode')->references('shortcode')->on('valutes')
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
        Schema::connection('report')->dropIfExists('accounts');
    }
}
