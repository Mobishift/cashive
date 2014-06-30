<?php namespace Admin;
use \View, \Input, \Log, \Redirect, \Carbon\Carbon, \Response ;
use \Campaign, \Reward, \Setting, \Cashive, \Payment, \User, \Faq;

function simple_array_to_string($array_){
    $res = array();
    foreach($array_ as $item){
        array_push($res, '"'.$item.'"');
    }
    return implode(",", $res);
}

class CampaignController extends \BaseController
{

    protected $layout = 'layouts.admin';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $campaigns = \Campaign::all();
        $this->layout->content = View::make('admin.campaign.index')
            ->with('active', 'campaign')->with('campaigns', $campaigns);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $campaign = new Campaign;
        $campaign->id = 0;
        $campaign->media_type = "image";
        $this->layout->content = View::make('admin.campaign.edit')
            ->with('active', 'campaign')->with('campaign', $campaign);
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
        $campaign = \Campaign::find($id);
        return \View::make('campaign.detail')->with('campaign', $campaign);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $campaign = \Campaign::find($id);
        $this->layout->content = \View::make('admin.campaign.edit')
            ->with('active', 'campaign')->with('campaign', $campaign);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        if($id == 0){
            $create = TRUE;
            $campaign = new Campaign;
            $campaign->save();
        }else{
            $create = FALSE;
            $campaign = Campaign::find($id);
        }
        $settings = Setting::find(1);


        $is_default = Input::get('campaign.is_default');
        if ($is_default == '1')
        {
            $settings->default_campaign_id = $campaign->id;
        }
        else if ($settings->default_campaign_id == $campaign->id)
        {
            $settings->default_campaign_id = NULL;
        }
        $settings->save();

        // Don't immediately update the campaign, since the API may still fail a validation.
        $campaign_data = Input::get('campaign');
        
        $campaign_data["goal_dollars"] = round(((double) $campaign_data["goal_dollars"])*100, 0);
        $campaign_data["completion_amount"] = $campaign_data["goal_dollars"];
        $campaign_data["min_payment_amount"] = round(((double) $campaign_data["min_payment_amount"])*100, 0);
        $campaign_data["fixed_payment_amount"] = round(((double) $campaign_data["fixed_payment_amount"])*100, 0);

        // Completely refresh the FAQ data
        $campaign->faqs()->delete();
        $faqs_data = Input::get('faq');
        foreach($faqs_data as $faq) 
        {
            if (! empty($faq['question']) )
            {
                $campaign->faqs()->create( array(
                        'question'         => $faq['question'],
                        'answer'        => $faq['answer'],
                        'sort_order'    => $faq['sort_order']
                    ));
            }
        }

        // Update the reward levels
        $rewards_data = Input::get('reward');
        foreach( $rewards_data as $reward_data )
        {
            if ( isset($reward_data['id']) && isset($reward_data['delete']) && $reward_data['delete'] == '1')
            {
                $reward = Reward::find($reward_data['id']);
                if ($reward->payments()->count() == 0)
                {
                    $reward->delete();
                }
            }
            else
            {
                if ( isset($reward_data['id']) )
                {
                    $reward = Reward::find($reward_data['id']);
                    $reward->title = $reward_data['title'];
                    $reward->image_url = $reward_data['image_url'];
                    $reward->description = $reward_data['description'];
                    $reward->delivery_date = $reward_data['delivery_date'];
                    $reward->number = $reward_data['number'];
                    $reward->price = $reward_data['price'];
                    $reward->collect_shipping_flag = isset($reward_data['collect_shipping_flag']) ? $reward_data['collect_shipping_flag'] : '0';
                    $reward->include_claimed = isset($reward_data['include_claimed']) ? $reward_data['include_claimed'] : '0';
                    if($reward->isDirty()){
                        $success = $reward->save();
                        if (!$success)
                        {
                            return Redirect::action('Admin\CampaignController@edit', $id)
                                ->withErrors('A reward field is missing or invalid');
                        }
                    }
                }
                else if ($reward_data['title'] != '')
                {
                    $campaign->rewards()->create( array(
                            'title'                    => $reward_data['title'],
                            'image_url'                => $reward_data['image_url'],
                            'description'            => $reward_data['description'],
                            'delivery_date'            => $reward_data['delivery_date'],
                            'number'                => $reward_data['number'],
                            'price'                    => $reward_data['price'],
                            'collect_shipping_flag'    => isset($reward_data['collect_shipping_flag']) ? $reward_data['collect_shipping_flag'] : '0',
                            'include_claimed'        => isset($reward_data['include_claimed']) ? $reward_data['include_claimed'] : '0'
                        ));
                }
            }
        }

        // if ( ! $campaign->valid() )
        // {

        // }

/*         $user_id = $campaign->production_flag ? $settings->production_admin_id : $settings->sandbox_admin_id; */

        // If the campaign has been promoted to production, delete all sandbox payments
        if ( $campaign->production_flag && $campaign->production_flag_changed )
        {
            // $campaign->payments()->delete();
        }

        $campaign->set_goal();

        // update the corresponding campaign on the Cashive Base
        // If the campaign has been promoted to production, create a new campaign on the Cashive Base
/*         if ($campaign->production_flag && $campaign->production_flag_changed) */
        if($create)
        {
/*             $campaign_data['user_id'] = $user_id; */
            $resp = Cashive::request('POST', 'campaigns', $campaign_data);
            Log::debug($resp->get_errcode());
        }
        else
        {
            $resp = Cashive::request('POST', 'campaigns/'.$campaign->ch_campaign_id, $campaign_data);
            Log::debug($resp->get_errcode());
        }
        if ($resp->is_success) 
        {
            $campaign->update_api_data($resp->get_data());
            $campaign->payment_type = $campaign_data["payment_type"];
            $campaign->media_type = $campaign_data["media_type"];
            if(isset($campaign_data["main_image_delete"])){
                $campaign->main_image_file_name = NULL;
                $campaign->main_image_content_type = NULL;
                $campaign->main_image_file_size = NULL;
                $campaign->main_image_updated_at = NULL;
            }elseif($campaign->media_type == "image" ){
                $main_image = Input::file('campaign.main_image');
                if($main_image){
                    $filesize = $main_image->getSize();
                    $destinationPath    = public_path().'/uploads/images/'; // The destination were you store the image.
                    $filename           = strtolower($main_image->getClientOriginalName()); // Original file name that the end user used for it.
                    $mime_type          = $main_image->getMimeType(); // Gets this example image/png
                    $filenames = explode('.', $filename);
                    $filename = $filename[0]."_".Carbon::now()->timestamp;
                    $extenson = $filename[count($filenames) - 1];
                    if (in_array($extenson, array("jpg", "jpeg", "bmp", "png", "gif"))){
                        $filename = $filename.".".$extenson;
                    }else{
                        $filename = $filename.".jpg";
                    }
                    $extension          = $main_image->getClientOriginalExtension(); // The original extension that the user used example .jpg or .png.
                    $upload_success     = $main_image->move($destinationPath, $filename); // Now we move the file to its new home.
                    
                    
                    $campaign->main_image_file_name = $filename;
                    $campaign->main_image_content_type = $mime_type;
                    $campaign->main_image_file_size = $filesize;
                    $campaign->main_image_updated_at = Carbon::now()->toDateTimeString();
                }

            }
            $campaign->save();
            return Redirect::action('Admin\CampaignController@index')
                ->withMessage('Campaign updated!');
        }
        else
        {
            if($create){
                $campaign->rewards()->delete();
                $campaign->faqs()->delete();
                $campaign->delete();
                return Redirect::action('Admin\CampaignController@create')
                    ->withErrors($resp->get_errmsg());
            }else{
                return Redirect::action('Admin\CampaignController@edit', $id)
                    ->withErrors($resp->get_errmsg());
            }
        }
        

        return Redirect::action('Admin\CampaignController@index');
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

    public function getCopy($id)
    {
        $campaign = Campaign::find($id)->replicate();
        $campaign->expiration_date = Carbon::now()->addDays(30)->toDateTimeString();
        $response = $campaign->update_to_base();
        if($response->is_success){
            $campaign->update_api_data($response->get_data());
            $campaign->save();
            $faqs = Faq::where("campaign_id", "=", $id);
            foreach($faqs->get() as $faq){
                $new_faq = $faq->replicate();
                $new_faq->campaign_id = $campaign->id;
                $new_faq->save();
            }
            $rewards = Reward::where("campaign_id", "=", $id);
            foreach($rewards->get() as $reward){
                $new_reward = $reward->replicate();
                $new_reward->campaign_id = $campaign->id;
                $new_reward->save();
            }
            return Redirect::action('Admin\CampaignController@edit', $campaign->id)
                    ->withMessage('Campaign copyed');
        }else{
            Log::error("CampaignController #{$campaign->id} getCopy failed, HTTP_CODE: {$response->http_code}, ERROR_RESPONSE: ".var_export($response->response, TRUE));
            return Redirect::action('Admin\CampaignController@index')
                ->withErrors('Campaign copy failed');
        }
    }

    public function getPayments($id, $format=".html")
    {
        $campaign = Campaign::find($id);
        $payment_id = Input::get('payment_id');
        $email = Input::get('email');
        $payments = Payment::where("campaign_id", "=", $campaign->id)->payed();
        if($payment_id){
            $payments = $payments->where("out_trade_no", "=", $payment_id);
        }elseif($email){
            $user = User::where("email", "=", $email)->first();
            if($user){
                $payments = $payments->where("uid", "=", $user->uid);
            }else{
                $payments = Payment::where("id", "=", "-1"); // make an empty query
            }
        }
        $payments = $payments->orderBy('created_at', 'ASC');
        if($format == ".csv"){
            $output = array();
            array_push($output, simple_array_to_string(array(
                    "Name", "Email", "Amount", "Quantity", "User Fee", "Date", "Status", "Payment ID"
                )));
            foreach ($payments->get() as $payment) {
                array_push($output, simple_array_to_string(array(
                    $payment->user->name, $payment->user->email, $payment->quantity,
                    $payment->amount, $payment->user_fee_amount, $payment->created_at,
                    $payment->status_en(), $payment->out_trade_no, 
                )));
            }
            $headers = array(
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="Payments.csv"',
            );

            return Response::make(implode("\n", $output), 200, $headers);
        }else{
            $this->layout->content = View::make('admin.campaign.payments')
                ->with('active', 'campaign')->with('campaign', $campaign)->with('payments', $payments)->with('payment_id', $payment_id)->with('email', $email);
        }
    }

    public function getHomepage()
    {
        
    }

    public function getCustomize()
    {
        
    }

    public function getSiteSettings()
    {
        
    }

    public function getPaymentSettings()
    {
        
    }

    public function getNotificationSettings()
    {
        
    }
}
