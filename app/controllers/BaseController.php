<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$settings = Setting::find(1);

			$user = null;
			if (Auth::check()) 
			{
				$user = Auth::user();
			}


			View::share('settings', $settings);
			View::share('user', $user);
			$this->layout = View::make($this->layout);
		}
	}

}
