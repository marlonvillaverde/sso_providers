<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});

*/
Route::get('/home', function(){ return view('welcome'); });

Route::get('loginsocial/{uuid}', [AuthenticatedSessionController::class, 'redirectToProvider']);

Route::get('loginsocial/callback/{provider}', [AuthenticatedSessionController::class, 'handleProviderCallBack']);

Route::get('/auth/saml2/metadata', [AuthenticatedSessionController::class, 'getProviderMetadata']);
//Route::get('/auth/saml2/metadata', function () {    return Socialite::driver('saml2')->getServiceProviderMetadata();});

Route::post('/auth/callback', function () {    $user = Socialite::driver('saml2')->user();});
