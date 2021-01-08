<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Hash;
use Exception;
use Tymon\JWTAuth\facades\JWTAuth;

class AuthController extends Controller
{

    public function signup(Request $request)
    {
        $encryptedPass = Hash::make($request->password);
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'birthdate' => 'required|date',
            'gender' => 'required'
        ]);

        $user = new User;
        try{
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $encryptedPass;
            $user->mobile = $request->mobile;
            $user->address = $request->address;
            $user->birthdate = $request->birthdate;
            $user->gender = $request->gender;
            $user->role = 2;
            $photo = '';
                if($request->photo!=''){
                    $photo = time().'.jpg';
                    file_put_contents('profiles/'.$photo,base64_decode($request->photo));
            $user->photo = $photo;
        }
        
        $user->save();
            $user->save();
            return $this->login($request);
        }
            catch(Exception $e){
                return response()->json([
                    'success' => false,
                    'message' => ''.$e
                ]);
            }
    }
  

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
            	'success' => false,
                'message' => 'Unauthorized'
            ]);
            
        $cred = $request->only(['email','password']);

        if(!$token=auth()->attempt($cred)){
            return response()->json([
                'success' => false,
                'message' => 'invalid credintials'
            ]);
        }
        
        $userKu = User::find(Auth::user()->id);
        $userKu->fcm_token = $request->fcm_token;
        $userKu->update();
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => Auth::user()
        ]);
    }
  
    public function logout(Request $request)
    {
        try{
            JWTAuth::invalidate(JWTAuth::parseToken($request->token));
            return response()->json([
                'success' => true,
                'message' => 'Logout Success'
            ]);
        }

        catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => ''.$e
            ]);
        }
    }
  

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}