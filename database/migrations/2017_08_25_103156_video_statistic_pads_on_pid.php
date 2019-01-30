<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoStatisticPadsOnPid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_statistic_pads_on_pid', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->string('country',5);
		$table->integer('id_src');
		$table->integer('pid');
		$table->integer('played');
		$table->integer('clicked');
		$table->float('ctr',4);
		$table->timestamps();
		$table->unique(['day', 'country', 'id_src', 'pid']);
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
		Schema::dropIfExists('video_statistic_pads_on_pid');
    }
}
