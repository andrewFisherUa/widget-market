<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WidgetVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('widget_videos', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('wid_id')->index();
		$table->integer('type');
		$table->integer('width');
		$table->integer('height');
		$table->integer('on_rus');
		$table->integer('on_cis');
		$table->integer('on_mobil');
		$table->integer('block_rus');
		$table->integer('block_cis');
		$table->integer('block_mobil');
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
		Schema::dropIfExists('widget_videos');
    }
}
