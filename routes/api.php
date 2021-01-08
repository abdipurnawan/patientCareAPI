<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


/*Route::group([
	'prefix' => 'auth'], function () {
    	Route::post('login', 'AuthController@login');
    	Route::post('signup', 'AuthController@signup');

    	Route::group(['middleware' => 'auth:api'], function() {
        	Route::get('logout', 'AuthController@logout');
        	Route::get('user', 'AuthController@user');
    });
});*/

//user
Route::post('login', 'AuthController@login');
Route::post('signup', 'AuthController@signup');
Route::get('logout', 'AuthController@logout');
Route::get('user', 'AuthController@user');
Route::post('homeUser', 'AuthController@homeUser')->middleware('jwtAuth');

Route::post('userRead','UserController@read');
Route::post('userEdit','UserController@edit');
Route::post('userEditPass','UserController@editPass');
Route::post('uploadPhoto', 'UserController@uploadPhoto');

//regis user
Route::post('regisSakit', 'PendaftaranController@daftar');
Route::post('getRegisSakitPending', 'PendaftaranController@getDaftarPending');
Route::post('getRegisSakitDirespon', 'PendaftaranController@getDaftarDirespon');
Route::post('editRegisSakit', 'PendaftaranController@editDaftar');
Route::post('getRegisSakitDetail', 'PendaftaranController@getDaftarDetail');
Route::post('deleteRegisSakit', 'PendaftaranController@deleteDaftar');

//ADMIN ROUTE
Route::post('getRegisSakitMasuk', 'PendaftaranController@getDaftarPendingAdmin');
Route::post('rejectRegisSakitMasuk', 'PendaftaranController@rejectRegistrasi');
Route::post('acceptRegisSakitMasuk', 'PendaftaranController@acceptRegistrasi');
Route::post('getRegisSakitDiresponAdmin', 'PendaftaranController@getDaftarDiresponAdmin');
Route::post('getAllAdmin', 'UserController@getAllAdmin');
Route::post('getAllUser', 'UserController@getAllUser');
Route::post('getUserDetail', 'UserController@userDetail');

Route::get('testFirebase', 'PendaftaranController@testFirebase');