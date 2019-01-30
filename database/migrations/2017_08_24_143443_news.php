<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class News extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('news', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('role');
		$table->integer('important');
		$table->integer('type');
		$table->string('header');
		$table->string('anoun');
		$table->text('body');
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
		Schema::dropIfExists('news');
    }
}
