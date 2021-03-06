<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewVideoStatPads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('new_video_stat_pads', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->string('country',5);
		$table->string('device',16);
		$table->integer('id_src');
		$table->integer('requested');
		$table->integer('ad_started');
		$table->integer('started');
		$table->integer('played');
		$table->integer('midpoint');
		$table->integer('third');
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
		Schema::dropIfExists('new_video_stat_pads');
    }
}

