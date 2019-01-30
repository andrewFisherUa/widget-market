<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoStatisticBlocksNewPlayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_statistic_blocks_new_player', function (Blueprint $table) {
		$table->increments('id');
		$table->date('date');
		$table->integer('id_block');
		$table->integer('requested');
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
		Schema::dropIfExists('video_statistic_blocks_new_player');
    }
}
