<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductSums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('product_sums', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('user_id');
		$table->integer('wid_id');
		$table->date('date');
		$table->integer('showed_top_advert');
		$table->integer('clicked_top_advert');
		$table->integer('our_clicked_top_advert');
		$table->float('summa_top_advert', 4);
		$table->integer('showed_yandex');
		$table->integer('clicked_yandex');
		$table->integer('our_clicked_yandex');
		$table->float('summa_yandex', 4);
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
		Schema::dropIfExists('product_sums');
    }
}
