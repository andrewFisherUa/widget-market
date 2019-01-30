<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoStatisticPids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_statistic_pids', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->integer('pid');
		$table->string('country',5);
		$table->integer('loaded');
		$table->integer('played');
		$table->integer('calc_played');
		$table->float('deep',4);
		$table->float('util',4);
		$table->float('dosm',4);
		$table->integer('clicked');
		$table->float('ctr',4);
		$table->float('summa',4);
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
		Schema::dropIfExists('video_statistic_pids');
    }
}
