<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_items;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends ApiController
{
    public static function create($request , $amounts , $token)
    {

        DB::beginTransaction();
        $order = Order::create([
            'user_id' => $request->user_id,
            'total_amount' => $amounts['total_amount'],
            'delivery_amount' => $amounts['delivery_amount'],
            'paying_amount' => $amounts['paying_amount']
        ]);


        foreach ($request->order_items as $order_item)
        {
            $product = Product::findOrFail($order_item['product_id']);
            Order_items::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $order_item['quantity'],
                'subtotal' => $order_item['quantity'] * $product->price,
            ]);
        }


        Transaction::create([
            'user_id' => $request->user_id,
            'order_id' => $order->id,
            'amount' => $amounts['paying_amount'],
            'token' => $token,
            'request_from' => $request->request_from

        ]);
        DB::commit();
    }

    public static function update($token , Request $request)
    {
        DB::beginTransaction();
        $transaction = Transaction::query()->where('token' , $token)->first();
        $transaction->update(
            [
                'status' => 1,
                'trans_id' => $request->transId
            ]
        );

        $order = Order::find($transaction->order_id);
        $order->update(
            [
                'status' => 1,
                'payment_status' => 1
            ]
        );

        DB::commit();
    }

}
