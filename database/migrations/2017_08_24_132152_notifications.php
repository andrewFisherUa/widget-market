<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Notifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('notifications', function (Blueprint $table) {
		$table->increments('id');
		$table->string('type');
		$table->string('data');
		$table->time('read_at');
		$table->integer('notifiable_id');
		$table->string('notifiable_type');
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
		Schema::dropIfExists('notifications');
    }
}
