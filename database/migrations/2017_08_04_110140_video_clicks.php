<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoClicks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_clicks', function (Blueprint $table) {
		$table->increments('id');
		$table->string('src');
		$table->string('fromUrl');
		$table->integer('count_view');
		$table->integer('count_clicks');
		$table->float('ratio');
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
		Schema::dropIfExists('video_clicks');
    }
}
