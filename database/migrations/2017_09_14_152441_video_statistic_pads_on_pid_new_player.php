<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoStatisticPadsOnPidNewPlayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_statistic_pads_on_pid_new_player', function (Blueprint $table) {
		$table->increments('id');
		$table->date('date');
		$table->string('country',8);
		$table->string('device',32);
		$table->integer('pid');
		$table->integer('id_src');
		$table->integer('requested');
		$table->integer('ad_started');
		$table->integer('started');
		$table->integer('played');
		$table->integer('completed');
		$table->integer('clicked');
		$table->float('poteri',4);
		$table->float('dosm',4);
		$table->float('util',4);
		$table->float('ctr',4);
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
		Schema::dropIfExists('video_statistic_pads_on_pid_new_player');
    }
}
