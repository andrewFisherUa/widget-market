<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoSource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_sources', function (Blueprint $table) {
		$table->increments('id');
		$table->string('src', 512);
		$table->integer('limit')->default(0);
		$table->integer('timeout')->default(0);
		$table->string('title',256);
		$table->string('client_title',256);
		$table->integer('ao')->default(0);
		$table->integer('category')->default(0);
		$table->integer('summa_rus')->nullable();
		$table->integer('summa_cis')->nullable();
		$table->integer('cheap')->nullable();
		$table->string('color')->nullable();
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
		Schema::dropIfExists('video_sources');
    }
}
