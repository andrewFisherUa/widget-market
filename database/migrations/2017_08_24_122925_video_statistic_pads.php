<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoStatisticPads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_statistic_pads', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->string('country',5);
		$table->integer('id_src');
		$table->integer('requested');
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
		Schema::dropIfExists('video_statistic_pads');
    }
}
