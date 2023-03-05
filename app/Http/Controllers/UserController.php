<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // for showing unverified seller
    public function index()
    {
        $users = User::where('verified', 'false')->get();
        return response()->json([
            'status'=>200,
            'users'=>$users,
        ]);

    }

    // for showing verified seller
    public function index2()
    {
        $users2 = User::where('verified', 'true')->get();
        return response()->json([
            'status'=>200,
            'users2'=>$users2,
        ]);
    }

    // registration of seller
    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'firstname'=>'required|string|regex:/^[a-zA-Z]+$/',
            'middlename'=>'required|string|regex:/^[a-zA-Z]+$/',
            'lastname'=>'required|string|regex:/^[a-zA-Z]+$/',
            'username'=>'required|unique:users,username',
            'mobilephone'=>'required|unique:users,mobilephone|regex:/^\+?\d{9-15}$/',
            'email'=>'required|unique:users,email|regex:/^.+@.+\..+$/i',
            'password'=>'required',
            'image'=>'required|image|mimes:jpeg,png,jpg',
            'userImage'=>'nullable|image|mimes:jpeg,png,jpg',
            'brgy'=>'required',
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
            $user = new User;
            $user->firstname=$req->input('firstname');
            $user->middlename=$req->input('middlename');
            $user->lastname=$req->input('lastname');
            $user->username=$req->input('username');
            $user->mobilephone=$req->input('mobilephone');
            $user->email=$req->input('email');

            // for image upload
            if($req->hasFile('image'))
            {
                $file = $req->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/user/',$filename);
                $user->image = 'uploads/user/'.$filename;
            }

            $user->verified=$req->input('verified');
            $user->brgy=$req->input('brgy');
            $user->privacy=$req->input('privacy');
            $user->password=Hash::make($req->input('password'));
            $user->save();

            return response()->json([
                'status'=> 200,
                'message' => 'Registration Submitted. Please wait for 5 minutes to login. Thank you!',
            ]);
        }

    }

    //login seller
    function login(Request $req)
    {
        $user= User::where('username',$req->username)->first();
        if(!$user || !Hash::check($req->password, $user->password))
        {
            return ["error"=> "Email or password is incorrect"];
        }
        if($user && Hash::check($req->password,$user->password) && !($user->verified == "true")){
            return ["notVerified"=>"User is not yet verified"];
        }
        return $user;
    }

    public function edit($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }

    // update seller info
    public function update(Request $req, $id)
    {
        $validator = Validator::make($req->all(),[
            'username'=>'nullable|max:20',
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
            $user = User::find($id);
            if($user)
            {
                $user->username=$req->input('username');
                $user->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'Username Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No User ID Found',
                ]);
            }
        }
    }

    public function showUserInfo($id)
    {
        $userInfo = User::where('id', $id)->get();
        return response()->json([
            'status'=> 200,
            'userInfo' => $userInfo,
        ]);
    }
    //user key to edit password
    public function editPassword($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }

    //update password
    public function updatePassword(Request $req, $id)
    {
        $validator = Validator::make($req->all(),[
            'password'=>'nullable|min:8',
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
            $user = User::find($id);
            if($user)
            {
                $user->password=Hash::make($req->input('password'));
                $user->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'User Account Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No User ID Found',
                ]);
            }
        }
    }

    //
    public function getUserInfo(Request $request, $id)
    {
        $user = User::where('id', '=', $id)->get();

        return $user;
    }

    public function editVerification($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }

    // verification button
    public function verification(Request $request, $id)
    {
        $verified = $request->get('verified');

        $user = User::find($id);
        $user->verified = $verified;
        $user->update();

        return response()->json([
            'status'=> 200,
            'message'=>'Seller Status Updated and Ready to Start Selling!',
        ]);
    
    }

    // edit image
    public function editImage($id)
    {
        $user = User::find($id);
        if($user)
        {
            return response()->json([
                'status'=> 200,
                'user' => $user,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No User ID Found',
            ]);
        }

    }
    //edit image
    public function updateImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'userImage'=>'required|image|mimes:jpeg,png,jpg|max:2048',
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
            $user = User::find($id);
            if($user)
            {
                if($request->hasFile('userImage'))
                {
                    $files = $request->file('userImage');
                    $extension = $files->getClientOriginalExtension();
                    $filename = time().'.'.$extension;
                    $files->move('uploads/userImage/',$filename);
                    $user->userImage = 'uploads/userImage/'.$filename;
                    $user->update();

                    return response()->json([
                        'status'=> 200,
                        'message'=>'User Account Updated Successfully',
                    ]);
                }
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No User ID Found',
                ]);
            }
        }
    }
}
