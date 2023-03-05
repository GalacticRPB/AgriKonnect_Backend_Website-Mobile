<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Shipping;

use Symfony\Component\Console\Output\ConsoleOutput;

class CheckoutController extends Controller
{
    public function placeorder(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'shippingaddress'=>'required',
            // 'mobilephone'=>'required',
            // 'modeofpayment'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages(),
            ]);
        }
        else 
        {

            $user_id = $request->input('user_id');
            $cart_id = $request->input('cart_id');
            $seller_id = $request->input('seller_id');
            $order_name = $request->input('order_name');
            $price = $request->input('price');
            $product_id = $request->input('product_id');
            $image = $request->input('image');
            $product_qty = $request->input('product_qty');
            $total_price = $request->input('total_price');
            $shippingfee = $request->input('shippingfee');
            $firstname = $request->input('firstname');
            $middlename = $request->input('middlename');
            $lastname = $request->input('lastname');
            $mobilephone = $request->input('mobilephone');
            $modeofpayment = $request->input('modeofpayment');

            $order = new Order;
            $order->user_id = $user_id;
            $order->cart_id = $cart_id;
            $order->seller_id = $seller_id;
            $order->order_name = $order_name;
            $order->price = $price;
            $order->image = $image;
            $order->product_id = $product_id;
            $order->product_qty = $product_qty;
            $order->shippingfee = $shippingfee;
            $order->total_price = $total_price;
            $order->firstname = $firstname;
            $order->middlename = $middlename;
            $order->lastname = $lastname;
            $order->shippingaddress = $request->input('shippingaddress');
            $order->mobilephone = $mobilephone;
            $order->modeofpayment = $modeofpayment;
            $order->save();
            
            $affected = Cart::where('id', $cart_id)->delete();

            return response()->json([
                'status'=>200,
                'message'=>'Order Placed Successfully',
                'affected'=>$affected,
            ]);
        }
    }

    public function showToPay(Request $request, $id)
    {
        $toPay = Order::where('user_id', $id)->get();
        
        return response()->json([
            'status'=>200,
            'toPay'=> $toPay,
        ]);

    }

   

}
