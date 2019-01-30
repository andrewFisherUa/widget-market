<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WidgetBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('widget_brands', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('wid_id')->index();
		$table->integer('block_rus');
		$table->integer('block_cis');
		$table->integer('block_mobil');
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
		Schema::dropIfExists('widget_brands');
    }
}
