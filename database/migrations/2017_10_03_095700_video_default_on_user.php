<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoDefaultOnUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('video_default_on_users', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('user_id');
		$table->integer('wid_type');
		$table->integer('pad_type');
		$table->integer('commission_rus');
		$table->integer('commission_cis');
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
		Schema::dropIfExists('video_default_on_users');
    }
}
