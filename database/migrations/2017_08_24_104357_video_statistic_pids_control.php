<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoStatisticPidsControl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_statistic_pids_control', function (Blueprint $table) {
		$table->increments('id');
		$table->date('day');
		$table->integer('pid');
		$table->string('country',5);
		$table->integer('id_src');
		$table->integer('played');
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
		Schema::dropIfExists('video_statistic_pids_control');
    }
}
