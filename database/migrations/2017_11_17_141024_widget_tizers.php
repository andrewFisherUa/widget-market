<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WidgetTizers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('widget_tizers', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('wid_id')->index();
		$table->string('name');
		$table->string('width');
		$table->string('height');
		$table->integer('cols');
		$table->integer('row');
		$table->integer('mobile');
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
		Schema::dropIfExists('widget_tizers');
    }
}
