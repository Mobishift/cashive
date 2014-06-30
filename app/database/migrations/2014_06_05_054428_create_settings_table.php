<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function($table)
	    {
	        $table->increments('id');
	        $table->string('site_name')->default('Cashive');
	        $table->string('logo_image_file_name')->nullable();
	        $table->string('homepage_content')->nullable();
	        $table->string('copyright_text')->nullable()->default('A Cashive Website');
	        $table->string('default_campaign_id')->nullable();
	        $table->string('sandbox_admin_id')->nullable();
	        $table->string('production_admin_id')->nullable();
	        $table->string('reply_to_email')->nullable();
	        $table->boolean('initialized_flag')->nullable()->default(false);
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
		Schema::drop('settings');
	}

}
