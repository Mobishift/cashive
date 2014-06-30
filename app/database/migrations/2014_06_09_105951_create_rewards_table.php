<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rewards', function($table)
	    {
	        $table->increments('id');
	        $table->string('campaign_id');
	        $table->string('title');
	        $table->string('description')->nullable();
	        $table->date('delivery_date');
	        $table->integer('number')->default(0);
	        $table->integer('price');
	        $table->string('image_url')->nullable();
	        $table->boolean('visible_flag')->default(false);
	        $table->boolean('collect_shipping_flag')->default(false);
	        $table->boolean('include_claimed')->nullable();
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
		Schema::drop('rewards');
	}

}
