<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function() {
	$settings = Setting::find(1);
	$campaign = Campaign::find($settings->default_campaign_id);
	if($campaign){
    	return Redirect::to('campaigns/' . $settings->default_campaign_id );
	}else{
        $user = Auth::user();
        $campaigns = Campaign::orderBy("created_at", "ASC");
        return View::make("home")->with("settings", $settings)->with("user", $user)->with("campaigns", $campaigns);
	}
});

Route::get('users/sign_in', array('as' => 'sign_in_path', 'uses' => 'UserController@showSignIn'));
Route::post('users/sign_in', array('uses' => 'UserController@doSignIn'));
Route::get('users/sign_out', array('as' => 'sign_out_path', 'uses' => 'UserController@getSignOut'));
Route::get('users/create', array('as' => 'sign_up_path', 'uses' => 'UserController@create'));
Route::resource('users', 'UserController');

Route::group(array('prefix' => 'admin', 'before' => array('auth', 'check_init', 'admin')), function() {
	Route::get('/', function() {
		return Redirect::action('Admin\\CampaignController@index');
	});
	 
	Route::match(array('GET', 'POST'), 'homepage', array('as' => 'admin_homepage_path', 'uses' => "Admin\\CampaignController@getHomepage"));	
	Route::get('customize', array('as' => 'admin_customize_path', 'uses' => "Admin\\CampaignController@getCustomize"));
	Route::get('site_settings', array('as' => 'admin_site_path', 'uses' => "Admin\\CampaignController@getSiteSettings"));
	Route::get('payment_settings', array('as' => 'admin_payment_path', 'uses' => "Admin\\CampaignController@getPaymentSettings"));
	Route::get('notification_settings', array('as' => 'admin_notification_path', 'uses' => "Admin\\CampaignController@getNotificationSettings"));

	Route::resource('campaigns', 'Admin\\CampaignController');
	Route::get('campaigns/{id}/copy', array('as' => 'admin_campaigns_copy_path', 'uses' => "Admin\\CampaignController@getCopy"));
	Route::get('campaigns/{id}/payments{format?}', array('as' => 'admin_campaigns_payments_path', 'uses' => "Admin\\CampaignController@getPayments"));
	
	Route::post('payments/{out_trade_no}/refund', array('as' => 'admin_payments_refund_path', 'uses' => 'Admin\\PaymentController@refund'));
});

/**
 * Campaign routes
 */
Route::resource('campaigns', 'CampaignController');
Route::group(array('before' => array('auth')), function() {
    Route::get('campaigns/checkout/{id}', "CampaignController@checkout");
    Route::post('campaigns/checkout/{id}/process', "CampaignController@checkoutProcess");
    Route::get('campaigns/{campaign_id}/payments/{payment_id}/success', "CampaignController@checkoutSuccess");
    Route::get('campaigns/{campaign_id}/payments/{payment_id}/error', "CampaignController@checkoutError");
});