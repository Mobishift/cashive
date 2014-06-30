<?php

class Payment extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';
    
    const STATUS_NO_PAY = 0;
    const STATUS_PAYED = 1;
    const STATUS_SETTLING = 4;
    const STATUS_SETTLED = 4;
    const STATUS_TO_REFUND = 7;
    const STATUS_REFUNDING = 8;
    const STATUS_REFUNDED = 9;
    
    public function user()
    {
        return $this->belongsTo('User', 'uid', 'uid');
    }

    public function status_en(){
        $STATUS_2_EN = array(
            self::STATUS_NO_PAY => "not payed",
            self::STATUS_PAYED => "payed",
            self::STATUS_SETTLING => "payed",
            self::STATUS_SETTLED => "payed",
            self::STATUS_TO_REFUND => "refunded",
            self::STATUS_REFUNDING => "refunded",
            self::STATUS_REFUNDED => "refunded",
        );
        return $STATUS_2_EN[$this->status];
    }

    public function scopePayed($query)
    {
        return $query->where('status', '<>', self::STATUS_NO_PAY);
    }

    public function scopeSuccess($query)
    {
        return $query->whereIn('status', array(self::STATUS_PAYED, self::STATUS_SETTLING, self::STATUS_SETTLED));
    }
    
    public function refresh(){
        $response = Cashive::request('GET', 'payments/'.$this->ct_payment_id);
        if($response->is_success){
            $this->update_api_data($response->get_data());
            $this->save();
        }else{
            Log::error("Payment #{$this->id} refresh failed, HTTP_CODE: {$response->http_code}, ERROR_RESPONSE: ".var_export($response->response, TRUE));
        }
    }
    
    public function update_api_data($data){
        $this->ct_payment_id = $data['id'];
        $this->out_trade_no = $data['out_trade_no'];
        $this->status = $data['status'];
        $this->amount = $data['amount'];
        $this->user_fee_amount = $data['user_fee_amount'];
        $this->admin_fee_amount = $data['admin_fee_amount'];
        $this->payment_url = $data['payment_url'];
        $this->uid = $data['contributor'];
        $this->payment_primary_type = $data['payment_primary_type'];
        $this->payment_sub_type = $data['payment_sub_type'];
        $this->redirect_url = $data['redirect_url'];
        $this->failure_url = $data['failure_url'];
        $this->payment_url = $data['payment_url'];
    }
    
    public function can_refund(){
        $REFUNDABLE_ARRAY = array(self::STATUS_PAYED);
        return in_array($this->status, $REFUNDABLE_ARRAY);
    }
    
    public function refund(){
        if(!$this->can_refund()){
            return array("code" => -1, "data" => "cannot refund");
        }
        $response = Cashive::request('POST', 'payments/'.$this->ct_payment_id.'/refund');
        if($response->is_success){
            $this->status = self::STATUS_TO_REFUND;
            $this->save();
            $data = $response->get_data();
        }else{
            Log::error("Payment #{$this->id} refund failed, HTTP_CODE: {$response->http_code}, ERROR_RESPONSE: ".var_export($response->response, TRUE));
            $data = $response->get_errmsg();
        }
        return array("code" => $response->get_errcode(), "data" => $data);
    }
}
