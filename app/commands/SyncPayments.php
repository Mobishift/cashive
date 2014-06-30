<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SyncPayments extends Command {
    
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cron:syncpayments';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Cron job to sync payments.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
	    define('PAGE_SIZE', 100);
	    
		$campaigns = Campaign::all();
		foreach($campaigns as $campaign){
		    $page = 1;
		    while(1){
        		$response = Cashive::request("GET", "campaigns/{$campaign->ch_campaign_id}/payments", array(
                    "page" => $page,
                    "page_size" => PAGE_SIZE,
        		));
        		if($response->is_success){
            		$data = $response->get_data();
            		$count = $data["count"];
            		$results = $data["results"];
            		foreach($results as $result){
                        $payment = Payment::where("ct_payment_id", "=", $result["id"])->first();
                        if($payment == NULL){
                            if(Payment::find($result["inner_trade_no"])){
                                echo "Payment #".$result["inner_trade_no"]." already exists when try to create\n";
                            }else{
                                $payment = new Payment;
                                $payment->id = $result["inner_trade_no"];
                                $payment->campaign_id = $campaign->id;
                                $payment->quantity = 1;
                                $payment->update_api_data($result);
                                $payment->save();
                                echo "Payment #".$result["inner_trade_no"]." created\n";
                            }
                        }else{
                            $payment->update_api_data($result);
                            echo "Payment #".$result["inner_trade_no"]." updated\n";
                        }
            		}
            		if($page * PAGE_SIZE >= $count){
                		break;
            		}
            		$page++;
        		}else{
            		echo "Campaign #{$this->id} sync_payments failed, HTTP_CODE: {$response->http_code}, ERROR_RESPONSE: ".var_export($response->response, TRUE)."\n";
            		break;
        		}
		    }
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
/* 			array('example', InputArgument::REQUIRED, 'An example argument.'), */
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
/* 			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null), */
		);
	}

}
