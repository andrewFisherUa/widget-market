<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewVideoStatPadsOnPid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('new_video_stat_pads_on_pid', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->string('country',5);
		$table->string('device',16);
		$table->integer('id_src');
		$table->integer('pid');
		$table->integer('played');
		$table->integer('clicked');
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
		Schema::dropIfExists('new_video_stat_pads_on_pid');
    }
}
