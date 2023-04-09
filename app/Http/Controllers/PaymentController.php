<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends ApiController
{
    public function send(Request $request)
    {
        $validation = Validator::make($request->all() , [

            'user_id' => 'required',
            'order_items' => 'required',
            'order_items.*.product_id' => 'required|integer',
            'order_items.*.quantity' => 'required|integer',
            'request_from' => 'required'
        ]);

        if ($validation->fails())
        {
            return $this->ErrorResponse($validation->getMessageBag() , 422);

        }




            $total_amount = 0;
            $deliverY_amount = 0;
            foreach ($request->order_items as $order_item)
            {
                $product = Product::findOrFail($order_item['product_id']);
                if ($product->quantity < $order_item['quantity'])
                {
                    return $this->ErrorResponse("this product quantity is incorrect" , 422);
                }

                $total_amount += $product->price * $product->quantity;
                $deliverY_amount  += $product->delivery_amount;
            }

            $paying_amount = $total_amount + $deliverY_amount;





        $amounts = [
            'total_amount' => $total_amount,
            'delivery_amount' =>$deliverY_amount,
            'paying_amount' => $paying_amount
        ];



        $api = env('GATEWAY_PAY_IR');
        $amount = $paying_amount;
        $mobile = "09931591988";
        $factorNumber = "شماره فاکتور";
        $description = "توضیحات";
        $redirect = env('CALL_BACK_URL');
        $result = $this->sendRequest($api, $amount, $redirect, $mobile, $factorNumber, $description);
        $result = json_decode($result);
        if($result->status) {
            OrderController::create($request , $amounts , $result->token);
            $go = "https://pay.ir/pg/$result->token";
            return $this->SuccessResponse("url from pay_ir" , 200 , [
                'url' => $go,
            ]);
        } else {
            return $this->ErrorResponse('pay failed' , 422 );
        }
    }


    public function verify(Request $request)
    {
        $validation = Validator::make($request->all() , [

            'token' => 'required',
            'status' => 'required',
        ]);

        if ($validation->fails())
        {
            return $this->ErrorResponse($validation->getMessageBag() , 422);

        }

        $api = env('GATEWAY_PAY_IR');
        $token = $request->token;
        $result = json_decode($this->verifyRequest($api,$token));
        if(isset($result->status)){
            if($result->status == 1){
                if (Transaction::query()->where('trans_id' , $request->transId)->exists())
                {
                    return $this->SuccessResponse('این تراکنش قبلا ثبت شده است' , 200 );

                }
                OrderController::update($token , $request);
                return $this->SuccessResponse('تراکنش باموفقیت انجام شد' , 200 );

            } else {
                return $this->ErrorResponse('تراکنش ناموفق بود' , 422 );
            }
        } else {
            if($request->status == 0){
                return $this->ErrorResponse('تراکنش با خطا مواجه شد'  , 422 );

            }
        }
    }

    public function sendRequest($api, $amount, $redirect, $mobile = null, $factorNumber = null, $description = null) {
        return $this->curl_post('https://pay.ir/pg/send', [
            'api'          => $api,
            'amount'       => $amount,
            'redirect'     => $redirect,
            'mobile'       => $mobile,
            'factorNumber' => $factorNumber,
            'description'  => $description,
        ]);
    }
    public function curl_post($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }


    public function verifyRequest($api, $token) {
        return $this->curl_post('https://pay.ir/pg/verify', [
            'api' => $api,
            'token' => $token,
        ]);
    }
}
