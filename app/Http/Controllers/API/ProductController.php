<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Delivered;
use App\Models\SellerDelivered;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProductController extends Controller
{
    //showing all the pro
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status'=> 200,
            'products'=>$products,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image'=>'required|image|mimes:jpeg,png,jpg|max:2048',
            'category'=>'required|max:191',
            'name'=>'required|max:191',
            'description'=>'required|max:191',
            'price'=>'required|integer|min:0',
            'quantity'=>'required|integer|min:0',

        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'errors'=> $validator->messages(),
            ]);
        }
        else
        {
            $product = new Product;

            if($request->hasFile('image'))
            {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/product/',$filename);
                $product->image = 'uploads/product/'.$filename;
            }

            $product->user_id = $request->input('user_id');
            $product->category = $request->input('category');
            $product->name = $request->input('name');
            $product->seller_name = $request->input('seller_name');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->quantity = $request->input('quantity');
            $product->save();

            return response()->json([
                'status'=> 200,
                'message'=>'Product Added Successfully',
            ]);
        }

    }

    public function edit($id)
    {
        $product = Product::find($id);
        if($product)
        {
            return response()->json([
                'status'=> 200,
                'product' => $product,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No Product ID Found',
            ]);
        }

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'category'=>'nullable|max:191',
            'name'=>'nullable|max:191',
            'description'=>'nullable|max:191',
            'price'=>'required|max:191|integer|min:0',
            'quantity'=>'required|max:191|integer|min:0',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'errors'=> $validator->messages(),
            ]);
        }
        else
        {
            $product = Product::find($id);
            if($product)
            {   
                $product->user_id = $request->input('user_id');
                $product->category = $request->input('category');
                $product->name = $request->input('name');
                $product->description = $request->input('description');
                $product->price = $request->input('price');
                $product->quantity = $request->input('quantity');
                $product->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'Product Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No Product ID Found',
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if($product)
        {
            $product->delete();
            return response()->json([
                'status'=> 200,
                'message'=>'Product Deleted Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No Product ID Found',
            ]);
        }
    }
    
    public function getUserProducts(Request $request, $id)
    {
        $products = Product::where('user_id', '=', $id)->get();

        return $products;
    }

    public function vegetable()
    {
        $products = Product::where('category', 'Like', 'Vegetables')->get();
        return response()->json([
            'status'=> 200,
            'products'=>$products,
        ]);
        
        
    }
    public function fruit()
    {
        $products = Product::where('category', 'Like', 'Fruits')->get();
        return response()->json([
            'status'=> 200,
            'products'=>$products,
        ]);
    }

    public function search($key)
    {
        return Product::where('name', 'Like', "%$key%")->get();
    }

    public function searchProduct($id, $key)
    {   
        $products = Product::where('user_id', $id)->where('name', 'Like', "%$key%")->get();

        return $products;
        
    }

    //showing of review for fruit
    public function viewfruit($product_id)
    {
        $review = Review::where('product_id', $product_id)->get();
        return response()->json([
            'status'=> 200,
            'reviews' =>$review,
        ]);
    }

    //showing of review for vegetables
    public function viewvegetable($product_id)
    {
        $review = Review::where('product_id', $product_id)->get();
        return response()->json([
            'status'=> 200,
            'reviews' =>$review,
        ]);
    }

    
    public function viewproductrecommendation($product_id)
    {
        $products = Review::where('product_id', $product_id)->get();
        return response()->json([
            'status'=> 200,
            'products' =>$products,
        ]);
    }

    public function recentSold($user_id)
    {
        $recent = SellerDelivered::where('seller_id', $user_id)->orderBy('updated_at', 'desc')->get();
        return response()->json([
            'status'=>200,
            'sellerdelivered'=>$recent,
        ]);
    }

    public function recommended()
    {
        
        $data = Product::orderBy('price')
        ->take(10)
        ->get();
        return response()->json([
            'status'=>200,
            'data'=> $data,       
        ]);

    }


    public function visualizationdate($id)
    {
        $date = SellerDelivered::select('created_at')->whereMonth('created_at', Carbon::now()->month)->where('seller_id', $id)->get();
        $date2 = SellerDelivered::select('created_at')->whereMonth('created_at', '11')->where('seller_id', $id)->get();
        
        $nowMonth = $date->count();
        $nextMonth = $date2->count();

        $data = [
            $nextMonth,
            $nowMonth,
        ];
        return response()->json([
            'status'=>200,
            'data'=> $data,
        ]);
    }

    public function myproducts(Request $request, $id) 
    {
        $products = Product::where('user_id', $id)->get();

        return response()->json([
            'status' => 200,
            'products' => $products,
        ]);
    }

    public function sample($id)
    { 
        $data = SellerDelivered::where('seller_id', $id)->get();
        $products = SellerDelivered::where('seller_id', $id)->select('order_name')->groupBy('order_name')->get();
        $qty = SellerDelivered::where('seller_id', $id)->selectRaw('product_id,SUM(order_qty) as total_qty')->groupBy('product_id')->get();
        $total = $data->sum->order_total;

        return response()->json([
            'status' => 200,
            'price' => $total,
            'products'=>$products,
            'qty'=>$qty

        ]);

    }

    public function orderMonthCount($id)
    {
        $orderCount = SellerDelivered::whereMonth('created_at', Carbon::now()->month)
        ->where('seller_id', $id)
        ->get();

        return response()->json([
            'status'=> 200,
            'orderCount'=> $orderCount->count()
        ]);
    }
}