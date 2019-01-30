<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('report')->create('type_operations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
			$table->string('shortcode');
			$table->string('title');
            $table->timestamps();
			$table->unique(['type', 'shortcode']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('report')->dropIfExists('type_operations');
    }
}
