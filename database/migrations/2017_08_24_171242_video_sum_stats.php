<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoSumStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_sum_stats', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->string('country',5);
		$table->integer('loaded');
		$table->integer('played');
		$table->integer('calc_played');
		$table->integer('complited');
		$table->float('deep',4);
		$table->float('util',4);
		$table->float('dosm',4);
		$table->integer('clicked');
		$table->float('ctr',4);
		$table->float('summa',4);
		$table->timestamps();
		$table->unique(['day', 'country']);
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
		Schema::dropIfExists('video_sum_stats');
    }
	
}
