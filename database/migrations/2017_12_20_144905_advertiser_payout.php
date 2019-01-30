<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvertiserPayout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('advertiser_payouts', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('sistem');
		$table->integer('user_id');
		$table->float('summa', 2);
		$table->integer('wmid')->nullable();
		$table->integer('wminvoiceid')->nullable();
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
		Schema::dropIfExists('advertiser_payouts');
    }
}
