<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
		$this->call('SettingTableSeeder');
/*
		$this->call('CampaignTableSeeder');
		$this->call('RewardTableSeeder');
*/

	}

}

class UserTableSeeder extends Seeder
{
	public function run()
	{
		DB::table('users')->delete();

		User::create( array(
			'name' => 'John Please',
			'email'		=> 'john@mobishift.com',
			'password'	=> Hash::make('abc123')
		));
	}
}

class SettingTableSeeder extends Seeder {

	public function run()
	{
		DB::table('settings')->delete();

		Setting::create(array(
				'site_name' => 'Cashive',
				'header_link_text' => 'A Cashive Open Site',
				'header_link_url' => 'www.mobishift.com',
				'homepage_content' => 'This is a demo homepage',
				'default_campaign_id'	=> 1
			));

	}	
}

class CampaignTableSeeder extends Seeder
{
	public function run()
	{
		DB::table('campaigns')->delete();

		Campaign::create( array(
			'name' 					=> 'Cashive Project Fundraising',
			'expiration_date'		=> new DateTime,
			'ch_campaign_id'		=> 'abc123',
			'min_payment_amount'	=> 100,
			'fixed_payment_amount'	=> 100,
			'goal_dollars'			=> 10000
		));
	}
}

class RewardTableSeeder extends Seeder
{
	public function run()
	{
		DB::table('rewards')->delete();

		Reward::create( array(
			'title' 			=> 'Cashive Reward I',
			'campaign_id'		=> '1',
			'delivery_date'		=> new DateTime,
			'number'			=> 100,
			'price'				=> 100
		));
	}
}