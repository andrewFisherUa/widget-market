<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('payment_commissions', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('user_id');
		$table->integer('who_add');
		$table->float('commission',2);
		$table->integer('status');
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
        //
		Schema::dropIfExists('payment_commissions');
    }
}
