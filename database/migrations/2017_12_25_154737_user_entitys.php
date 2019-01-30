<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserEntitys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('user_entitys', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('type_payout');
		$table->integer('user_id');
		$table->integer('form');
		$table->string('name');
		$table->string('position');
		$table->string('firm_name');
		$table->text('legale_male');
		$table->text('fact_male');
		$table->string('inn');
		$table->string('kpp');
		$table->string('ogrn');
		$table->string('okved');
		$table->string('name_bank');
		$table->string('account');
		$table->string('kor_account');
		$table->string('bik');
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
		Schema::dropIfExists('user_entitys');
    }
}
