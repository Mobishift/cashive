<?php

class CampaignController extends \BaseController {

	protected $layout = 'layouts.base';

	public function __construct()
	{
		$this->beforeFilter('check_init', array('except' => array(
				'store', 'update'
			)));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('campaign.list');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$campaign = Campaign::find($id);
		return View::make('campaign.detail')->with('campaign', $campaign);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function checkout($id)
	{
	    $campaign = Campaign::find($id);
		return View::make('campaign.checkout')->with('campaign', $campaign);
	}
    
    public function checkoutProcess($id)
    {
        $campaign = Campaign::find($id);
        $user = Auth::user();
        
        $rules = array(
            'amount'    => 'required|numeric',
            'payment_primary_type' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ( $validator->fails() )
        {
            return Redirect::action('CampaignController@checkout', $id)
                ->withInput()
                ->withErrors($validator->errors());
        }
        else 
        {
            $quantity = (int) Input::get('quantity');
            if($quantity <= 0){
                $quantity = 1;
            }
            $reward_id = Input::get('reward');
            $reward = NULL;
            
            $amount = (double) Input::get('amount');
            $amount = round($amount, 2);

            if($reward_id != NULL){
                $reward = Reward::find($reward_id);
                if($reward->price > $amount){
                    $reward = NULL;
                }
            }
            if($campaign->payment_type == "min" and $amount < $campaign->simple_min_payment_amount()){
                return Redirect::action('CampaignController@checkout', $id)
                    ->withInput()
                    ->withErrors('amount must greater than min payment amount');
            }
            if($amount < 0.01){
                return Redirect::action('CampaignController@checkout', $id)
                    ->withInput()
                    ->withErrors('amount must greater than zero');
            }
            $data = array(
                'amount' => $amount,
                'quantity' => $quantity,
                'payment_primary_type' => Input::get('payment_primary_type'),
                'reward' => $reward,
            );
            $payment = $campaign->make_payment($user, $data);
            if($payment){
                return Redirect::to($payment->payment_url);
            }else{
                return Redirect::action('CampaignController@checkout', $id)
                    ->withInput()
                    ->withErrors('create payment failed');
            }
        }
    }

	public function checkoutSuccess($campaign_id, $payment_id)
	{
	    $campaign = Campaign::find($campaign_id);
	    $payment = Payment::find($payment_id);
        $payment->refresh();
		return View::make('campaign.checkoutsuccess')->with('campaign', $campaign);
	}

	public function checkoutError($campaign_id, $payment_id)
	{
	    $campaign = Campaign::find($campaign_id);
	    $payment = Payment::find($payment_id);
        $payment->refresh();
		return View::make('campaign.checkouterror')->with('campaign', $campaign);
	}

}
