<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommissionGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('сommission_groups', function (Blueprint $table) {
		$table->string('commissiongroupid',8)->primary();
		$table->integer('type');
		$table->string('name');
		$table->string('label');
		$table->float('value');
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
		Schema::dropIfExists('сommission_groups');
    }
}
