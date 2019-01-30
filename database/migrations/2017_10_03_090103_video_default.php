<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_defaults', function (Blueprint $table) {
		$table->increments('id');
		$table->string('name');
		$table->integer('block_rus');
		$table->integer('block_mobil');
		$table->integer('block_cis');
		$table->string('commission_rus');
		$table->string('commission_cis');
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
		Schema::dropIfExists('video_defaults');
    }
}
