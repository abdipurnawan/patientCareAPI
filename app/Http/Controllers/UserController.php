<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Hash;
use Exception;
use Tymon\JWTAuth\facades\JWTAuth;

class userController extends Controller{
    
    public function read(Request $request){
        // AMBIL USER
        $user = User::find(Auth::user()->id);
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function edit(Request $request){
        $user = User::find(Auth::user()->id);

        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->birthdate = $request->birthdate;
        $user->gender = $request->gender;
        $user->address = $request->address;
        $photo = '';
        if($request->photo!=''){
            $photo = time().'.jpg';
            file_put_contents('profiles/'.$photo,base64_decode($request->photo));
        $user->photo = $photo;
        }
        $user->save();

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function editPass(Request $request){
        $user = User::find(Auth::user()->id);

        $savedPass = $user->password;
        $getPass = $request->password;
        $newPass = Hash::make($request->newPass);
        if(Hash::check($getPass, $savedPass)){
            $user->password = $newPass;
            $user->save();
            return response()->json([
                'success' => true
            ]);
        }else{
            return response()->json([
                'success' => false
            ]);
        }
    }
    
    public function getAllAdmin(){
        $user = User::where('role','1')->get();
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
    
    public function getAllUser(){
        $user = User::where('role','2')->get();
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
    
    public function userDetail(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
    
    
}