<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('campaigns', function($table)
	    {
	        $table->increments('id');
	        $table->string('name');
	        $table->datetime('expiration_date');
	        $table->string('ch_campaign_id');
	        $table->string('media_type')->nullable();
	        $table->string('main_image_file_name')->nullable();
	        $table->string('main_image_content_type')->nullable();
	        $table->string('main_image_file_size')->nullable();
	        $table->datetime('main_image_updated_at')->nullable();
	        $table->string('video_embed_id')->nullable();
	        $table->string('video_placeholder_file_name')->nullable();
	        $table->string('video_placeholder_content_type')->nullable();
	        $table->string('video_placeholder_file_size')->nullable();
	        $table->string('video_placeholder_updated_at')->nullable();
	        $table->string('contributor_reference')->default('backer');
	        $table->string('progress_text')->default('funded');
	        $table->string('primary_call_to_action_button')->default('Contribute');
	        $table->string('primary_call_to_action_description')->nullable();
	        $table->string('secondary_call_to_action_button')->default('Contribute');
	        $table->string('secondary_call_to_action_description')->nullable();
	        $table->text('main_content')->nullable();
	        $table->text('checkout_sidebar_content')->nullable();
	        $table->text('confirmation_page_content')->nullable();
	        $table->text('confirmation_email_content')->nullable();
	        $table->string('payment_type')->default('any');
	        $table->integer('min_payment_amount');
	        $table->integer('fixed_payment_amount');
	        $table->boolean('apply_processing_fee')->default(true);
	        $table->string('slug')->nullable();
	        $table->boolean('published_flag')->default(false);
	        $table->string('goal_type')->default('dollars');
	        $table->integer('goal_dollars');
	        $table->integer('goal_orders')->default(0);
	        $table->boolean('production_flag')->default(false);
	        $table->boolean('include_rewards')->default(false);
	        $table->string('reward_reference')->nullable();
	        $table->boolean('collect_additional_info')->default(false);
	        $table->string('additional_info_label')->nullable();
	        $table->boolean('include_comments')->default(false);
	        $table->string('comments_short_name')->nullable();
	        $table->boolean('include_rewards_claimed')->default(false);
	        $table->integer('stat_number_of_contribution')->default(0);
	        $table->integer('stat_unique_contributors')->default(0);
	        $table->integer('stat_raised_amount')->default(0);
	        $table->integer('stat_completion_percentage')->default(0);

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
		Schema::drop('campaigns');
	}

}
