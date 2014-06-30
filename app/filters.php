<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::action('UserController@showSignIn');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('check_init', function()
{
	$settings = Setting::find(1);
	if ( ! $settings->initialized_flag )
	{
		$current_user = Auth::user();
		if ($current_user) 
		{
			$current_user->admin = true;
			$settings->initialized_flag = true;
			$settings->reply_to_email = $current_user->email;

			// create the admin user on the Cashive Base
			$response = Cashive::request('POST', 'contributors', array(
								'email'		=> $current_user->email,
								'nickname'	=> $current_user->name,
								'realname'	=> $current_user->name
							));
			Log::debug( $response->get_data() );
			if ( $response->is_success ) 
			{
				// Cashive Base returns okay
				$settings->sandbox_admin_id = $response->get_data()['uid'];
				$settings->save();
				$current_user->uid = $response->get_data()['uid'];
				$current_user->save();
			}
			else
			{
				$settings->initialized_flag = false;
				$settings->save();
				Auth::logout();
				return Redirect::route('sign_up_path')->withErrors("An error occurred, please contact support@mobishift.com. " 
					. $response->get_errmsg() );
			}

			return Redirect::to('/admin');
		}
		else
		{
			if (User::count() == 0)
			{
				return Redirect::route('sign_up_path')->withMessage("Please create an account below to initialize the app.");
			}
			else
			{
				return Redirect::route('sign_in_path')->withMessage("Please sign in below.");
			}
		}
	}
});

Route::filter('admin', function(){

    if ( ! Auth::user()->admin)
    {
        return Redirect::route('sign_in_path')
         ->withMessage('No Admin, sorry.');
    }

});