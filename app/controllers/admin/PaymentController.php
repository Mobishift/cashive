<?php namespace Admin;
use \BaseController, \Response;
use \Payment, \Cashive;

class PaymentController extends BaseController
{

    public function refund($out_trade_no){
        $payment = Payment::where("out_trade_no", "=", $out_trade_no)->first();
        if(!$payment){
            return Response::json(array("code" => -2, "data" => "payment does not exist"), 400);
        }
        $res = $payment->refund();
        if($res['code'] == 0){
            $status = 200;
        }else{
            $status = 400;
        }
        return Response::json($res, $status);
    }

}
