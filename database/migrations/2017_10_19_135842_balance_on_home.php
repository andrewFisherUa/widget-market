<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BalanceOnHome extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('balance_on_homes', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->integer('user_id');
		$table->float('video_commission', 4);
		$table->float('product_commission', 4);
		$table->float('referal_commission', 4);
		$table->float('manager_commission', 4);
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
		Schema::dropIfExists('balance_on_homes');
    }
}
