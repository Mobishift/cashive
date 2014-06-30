<?php

class Campaign extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'campaigns';

    public function simple_goal_dollars(){
        return (float) $this->goal_dollars / 100.0;
    }
    
    public function simple_min_payment_amount(){
        return (float) $this->min_payment_amount / 100.0;
    }

    public function simple_fixed_payment_amount(){
        return (float) $this->fixed_payment_amount / 100.0;
    }

    public function main_image_file_path(){
        return '/uploads/images/'.$this->main_image_file_name;
    }

    public function rewards()
    {
        return $this->hasMany('Reward')->orderBy('price', 'ASC');
    }

    public function faqs()
    {
        return $this->hasMany('Faq');
    }

    public function orders() 
    {
        return intval($this->stats_raised_amount / 1); //$this->fixed_payment_amount);
    }

    public function raised_amount()
    {
        $res = $this->stats_raised_amount;
        if(!$res){
            $res = 0;
        }
        return $res;
    }

    public function completion_percentage()
    {
        return $this->raised_amount() / $this->goal_dollars * 100.0;
    }

    public function expired()
    {
        $expiration = new DateTime($this->expiration_date);
        $interval = $expiration->diff(new DateTime('today'));
        return ! ($interval->format('%a') > 0);
    }

    public function reward_claimed()
    {
        return 0;
    }

    public function number_of_contributions()
    {
        return 0;
    }

    public function set_goal()
    {
        if ($this->goal_type == 'orders')
        {
            $this->goal_dollars = ($this->fixed_payment_amount * $this->goal_orders) * 100;
        }
    }

    public function update_to_base(){
        $resp = Cashive::request('POST', 'campaigns', array(
            "title" => $this->name,
            "completion_amount" => $this->goal_dollars,
            "expiration_date" => $this->expiration_date,
        ));
        return $resp;
    }

    public function update_api_data($campaign)
    {
        $this->ch_campaign_id = $campaign['id'];
        $this->name = $campaign['title'];
        $this->stat_number_of_contribution = $campaign['number_of_contributions'];
        $this->stat_raised_amount = $campaign['raised_amount'];
        $this->stat_completion_percentage = $campaign['complete_percent'];
        $this->stat_unique_contributors = $campaign['unique_contributors'];
        $this->goal_dollars = $campaign['completion_amount'];
        $this->expiration_date = $campaign['expiration_date'];
        $this->min_payment_amount = $campaign['min_payment_amount'];
        $this->fixed_payment_amount = $campaign['fixed_payment_amount'];
        // $this->is_completed = $campaign['is_completed'] == 0 ? false : true;
        // $this->is_expired = $campaign['is_expired'] == 0 ? false : true;
        // $this->is_paid = $campaign['is_paid'] == 0 ? false : true;
    }
    
    public function make_payment($user, $data){
        $payment = new Payment;
        $payment->campaign_id = $this->id;
        $payment->uid = $user->uid;
        $payment->amount = $data['amount'];
        $payment->payment_primary_type = $data['payment_primary_type'];
        $payment->quantity = $data['quantity'];
        
        $reward = $data['reward'];
        if($reward != NULL){
            $payment->reward_id = $reward->id;
        }
        
        $payment->save();

        $payment->redirect_url = action('CampaignController@checkoutSuccess', array("campaign_id" => $this->id, "payment_id" => $payment->id));
        $payment->failure_url = action('CampaignController@checkoutError', array("campaign_id" => $this->id, "payment_id" => $payment->id));
        
        $response = Cashive::request('POST', "campaigns/{$this->ch_campaign_id}/create_payment", array(
            'contributor_id' => $payment->uid,
            'inner_trade_no' => $payment->id,
            'payment_type' => $payment->payment_primary_type,
            'amount' => $payment->amount,
            'redirect_url' => $payment->redirect_url,
            'failure_url' => $payment->failure_url,
        ));
        if($response->is_success){
            $data = $response->get_data();
            $payment->update_api_data($data);
            $payment->save();
            return $payment;
        }else{
            $payment->delete();
            Log::error("Campaign #{$this->id} make_payment failed, HTTP_CODE: {$response->http_code}, ERROR_RESPONSE: ".var_export($response->response, TRUE));
            return NULL;
        }
    }
}
