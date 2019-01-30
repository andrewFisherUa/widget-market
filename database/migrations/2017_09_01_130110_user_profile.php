<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('user_profiles', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('user_id');
		$table->float('balance', 18,2)->nullable();
		$table->string('name');
		$table->string('firstname');
		$table->string('lastname');
		$table->string('email',255)->unique();
		$table->integer('manager')->nullable();
		$table->string('phone',32)->nullable();
		$table->string('icq',128)->nullable();
		$table->string('skype',128)->nullable();
		$table->text('avatar')->nullable();
		$table->integer('id_for_pads')->nullable();
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
		Schema::dropIfExists('user_profiles');
    }
}
