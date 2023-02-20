<?php

namespace App\Http\Controllers\API;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Email;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
class CustomerController extends Controller
{
    //
    
    function registerCustomer(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'firstname'=>'required',
            'middlename'=>'required',
            'lastname'=>'required',
            'username'=>'required|unique:customers,username',
            'mobilephone'=>'required|unique:customers,mobilephone|max:11',
            'email'=>'required|unique:customers,email',
            'privacy'=>'required|accepted',
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
            $otp = rand(100000, 999999);

            $customer = new Customer;
            $customer->firstname=$req->input('firstname');
            $customer->middlename=$req->input('middlename');
            $customer->lastname=$req->input('lastname');
            $customer->username=$req->input('username');
            $customer->mobilephone=$req->input('mobilephone');
            $customer->email=$req->input('email');
            $customer->privacy=$req->input('privacy');
            $customer->otp = $otp;
            $customer->password=Hash::make($req->input('password'));
            $customer->save();

            $email = $req->input('email');

            if($customer)
            {
                Mail::to($email)->send(new Email($email, $otp));
                return new JsonResponse([
                    'success' => true,
                    'customer' => $customer,
                    'message' => 'Thank you for registering! Please input the otp code that we send to your email.'
                ], 200);
            }
            
            // return response()->json([
            //     'status'=> 200,
            //     'message' => 'Successfully Registered',
            //     'customer'=>$customer
            // ]);
        }
        
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
            'otp' => 'required|max:191',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'errors'=> $validator->messages(),
            ]);
        }

        $customer = Customer::where([['email','=',$request->email],['otp','=',$request->otp]])->first();
        if($customer){
            Customer::where('email','=',$request->email)->update(['otp' => '000000', 'verified' => 'true']);

            return new JsonResponse([
                'success' => true,
                'message' => 'Thank you for registering! Please input the otp code that we send to your email.'
            ], 200);
        }
        else{
            return response()->json([
                'status'=> 422,
                'errors'=> $validator->messages(),
            ]);
        }
    }

    //
    function loginCustomer(Request $req)
    {
        $customer= Customer::where('username',$req->username)->first();
        if(!$customer || !Hash::check($req->password, $customer->password))
        {
            return ["error"=> "Email or password is incorrect"];
        }
        if($customer && Hash::check($req->password,$customer->password) && !($customer->verified == "true")){
            return ["notVerified"=>"Customer is not yet verified"];
        }
        return $customer;

        $customer = Customer::where('email',$req->email)->first();
        if(!$customer || !Hash::check($req->password,$customer->password)){
            return ["error"=>"Email address or Password is not matched"];
        }
        if($customer && Hash::check($req->password,$customer->password) && !($customer->verified == "true")){
            $otp = rand(100000,999999); //add
            $email = $req->email;
            Mail::to($email)->send(new Email($email, $otp)); // add $otp
            Customer::where('email','=',$req->email)->update(['otp' => $otp]);
            return ["notVerified"=>"Customer is not yet verified"];
        }
        return $customer;
    }

    public function editCustomer($id)
    {
        $customer = Customer::find($id);
        if($customer)
        {
            return response()->json([
                'status'=> 200,
                'customer' => $customer,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No Customer ID Found',
            ]);
        }

    }

    public function updateCustomer(Request $req, $id)
    {
        $validator = Validator::make($req->all(),[
            'firstname'=>'nullable',
            'middlename'=>'nullable',
            'lastname'=>'nullable',
            'mobilephone'=>'required|unique:users,mobilephone|max:11',
            'email'=>'required||unique:users,email',
            'password'=>'nullable|min:8',
            'address'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'validationErrors'=> $validator->messages(),
            ]);
        }
        else
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $customer->firstname=$req->input('firstname');
                $customer->middlename=$req->input('middlename');
                $customer->lastname=$req->input('lastname');
                $customer->mobilephone=$req->input('mobilephone');
                $customer->email=$req->input('email');
                $customer->password=Hash::make($req->input('password'));
                $customer->address=$req->input('address');
                $customer->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'Customer Account Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No Customer ID Found',
                ]);
            }
        }
    }

    public function getCustomerCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', '=', $id)->get();

        return $customer;
    }
}