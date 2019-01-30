<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('auth_logs', function (Blueprint $table) {
		$table->increments('id');
		$table->date('date');
		$table->integer('user_id');
		$table->string('ip_adress',255);
		$table->text('user_agent');
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
		Schema::dropIfExists('auth_logs');
    }
}
