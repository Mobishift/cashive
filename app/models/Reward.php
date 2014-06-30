<?php

class Reward extends Eloquent {

	protected $table = 'rewards';

	protected $fillable = array('title', 'description', 'delivery_date', 'number', 'price', 'image_url', 'visible_flag', 'collect_shipping_flag', 'include_claimed');

	public function payments()
	{
		return $this->hasMany('Payment');
	}
	
	public function visible()
	{
	    return TRUE;
/* 		return $this->visible_flag; */
	}
	
	public function sold_number(){
    	return Payment::where("reward_id", "=", $this->id)->success()->count();
	}
	
	public function sold_out(){
	    if($this->is_unlimited()){
    	    return FALSE;
	    }
    	$sold_number = $this->sold_number();
        return ($sold_number >= $this->number);
	}
	
	public function is_unlimited(){
    	return (!$this->number);
	}

}