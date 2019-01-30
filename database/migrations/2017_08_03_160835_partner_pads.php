<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PartnerPads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
			Schema::create('partner_pads', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('user_id');
		$table->string('domain')->unique();
		$table->integer('status');
		$table->integer('type')->nullable();
		$table->string('id_categories')->nullable();
		$table->integer('video_categories')->nullable();
		$table->string('stcurl');
		$table->string('stclogin')->nullable();
		$table->string('stcpassword')->nullable();
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
		Schema::dropIfExists('partner_pads');
    }
}
