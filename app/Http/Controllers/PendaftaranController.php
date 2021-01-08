<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Poli;
use App\Pendaftaran;
use Hash;
use Exception;
use Tymon\JWTAuth\facades\JWTAuth;

class PendaftaranController extends Controller
{
    
    public function daftar(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $idUser = $user->id;
        $pendaftaran = new Pendaftaran();

            $pendaftaran->id_user = $idUser;
            $pendaftaran->poli = $request->poli;
            $pendaftaran->keluhan = $request->keluhan;
            $pendaftaran->penyakit_bawaan = $request->penyakit_bawaan;
            $pendaftaran->tinggi_badan = $request->tinggi_badan;
            $pendaftaran->berat_badan = $request->berat_badan;
            $pendaftaran->status = "pending";
            $pendaftaran->save();
            
            $ch=curl_init("https://fcm.googleapis.com/fcm/send");
            $header=array("Content-Type:application/json", "Authorization: key=AAAAL8Xo3ks:APA91bHJNwl5NTpwJMOxg2GgEzsVorY975RwOTFCvorwW267NuCxaAlW0xeCguuwnt_G9tUn5N3Kv3HGmhMbw6Vcwfnc-SynXcCTd3gTZ70Jeduxpa99LDLT7m7xTY790CZXkqouNplx");
            $data=json_encode(array("to"=>"/topics/admin","data"=>array("title"=>"Halo Admin Patient Care", "message"=>"Ada Pendaftaran Baru yang Masuk!")));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_exec($ch);

    }
    
    public function getDaftarPending()
    {
        $user = User::find(Auth::user()->id);
        $idUser = $user->id;
        
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where(['id_user' => $idUser, 'status' => "pending"])->orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'pendaftaran' => $allDaftar
        ]);
    }
    
    public function getDaftarDirespon()
    {
        $user = User::find(Auth::user()->id);
        $idUser = $user->id;
        
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where(['id_user' => $idUser, 'status' => "accepted"])->orWhere(function($q) use ($idUser){
            $q->where(['id_user' => $idUser, 'status' => "rejected"]);
            })->orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'pendaftaran' => $allDaftar
        ]);
    }
    
    public function editDaftar(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $idUser = $user->id;
        
        $pendaftaranObject = new Pendaftaran();
            $pendaftaran = $pendaftaranObject->where('id', $request->id_regis)->first();
            $pendaftaran->id_user = $idUser;
            $pendaftaran->poli = $request->poli;
            $pendaftaran->keluhan = $request->keluhan;
            $pendaftaran->penyakit_bawaan = $request->penyakit_bawaan;
            $pendaftaran->tinggi_badan = $request->tinggi_badan;
            $pendaftaran->berat_badan = $request->berat_badan;
            $pendaftaran->update();
        
            return response()->json([
                'success' => true
            ]);
    }
    
    public function getDaftarDetail(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $idUser = $user->id;
        
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where('id', $request->id)->first();
        return response()->json([
            'success' => true,
            'pendaftaran' => $allDaftar
        ]);
    }
    
    public function deleteDaftar(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $idUser = $user->id;
        
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where('id', $request->id)->delete();
        return response()->json([
            'success' => true
        ]);
    }

  

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    
    
    //FOR ADMIN
    public function getDaftarPendingAdmin()
    {
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where('status' , "pending")->orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'pendaftaran' => $allDaftar
        ]);
    }
    
    public function rejectRegistrasi(Request $request)
    {
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where('id', $request->id)->first();
        $allDaftar->status = "rejected";
        $allDaftar->update();
        
        $userID = $allDaftar->id_user;
        
        $user = new User();
        $userFcmToken = $user->where('id', $userID)->get(['fcm_token'])->first();
        
        $ch=curl_init("https://fcm.googleapis.com/fcm/send");
        $header=array("Content-Type:application/json", "Authorization: key=AAAAL8Xo3ks:APA91bHJNwl5NTpwJMOxg2GgEzsVorY975RwOTFCvorwW267NuCxaAlW0xeCguuwnt_G9tUn5N3Kv3HGmhMbw6Vcwfnc-SynXcCTd3gTZ70Jeduxpa99LDLT7m7xTY790CZXkqouNplx");
        $data=json_encode(array("to"=>$userFcmToken->fcm_token,"data"=>array("title"=>"Halo Sahabat Patient Care", "message"=>"Pendaftaranmu Tidak Dapat Disetujui :(")));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
        
    }
    
    public function acceptRegistrasi(Request $request)
    {
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where('id', $request->id)->first();
        
        
        $allDaftar->tgl_regis = $request->tanggal;
        $allDaftar->status = "accepted";
        $allDaftar->update();
        
        $userID = $allDaftar->id_user;
        
        $user = new User();
        $userFcmToken = $user->where('id', $userID)->get(['fcm_token'])->first();
        
        $ch=curl_init("https://fcm.googleapis.com/fcm/send");
        $header=array("Content-Type:application/json", "Authorization: key=AAAAL8Xo3ks:APA91bHJNwl5NTpwJMOxg2GgEzsVorY975RwOTFCvorwW267NuCxaAlW0xeCguuwnt_G9tUn5N3Kv3HGmhMbw6Vcwfnc-SynXcCTd3gTZ70Jeduxpa99LDLT7m7xTY790CZXkqouNplx");
        $data=json_encode(array("to"=>$userFcmToken->fcm_token,"data"=>array("title"=>"Halo Sahabat Patient Care", "message"=>"Pendaftaranmu Telah Diterima!")));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
        

    }
    
    public function getDaftarDiresponAdmin()
    {
        $Daftar = new Pendaftaran();
        $allDaftar = $Daftar->where('status' , "accepted")->orWhere(function($q){
            $q->where('status' , "rejected");
            })->orderBy('id','DESC')->get();
        return response()->json([
            'success' => true,
            'pendaftaran' => $allDaftar
        ]);
    }
    
    public function testFirebase(Request $request){
        $ch=curl_init("https://fcm.googleapis.com/fcm/send");
        $header=array("Content-Type:application/json", "Authorization: key=AAAAL8Xo3ks:APA91bHJNwl5NTpwJMOxg2GgEzsVorY975RwOTFCvorwW267NuCxaAlW0xeCguuwnt_G9tUn5N3Kv3HGmhMbw6Vcwfnc-SynXcCTd3gTZ70Jeduxpa99LDLT7m7xTY790CZXkqouNplx");
        $data=json_encode(array("to"=>"/topics/allDevices","data"=>array("title"=>"TEST!", "message"=>"ini pesan")));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
    }
    
}