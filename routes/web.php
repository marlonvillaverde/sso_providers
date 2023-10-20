<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\AvailableProvidersController;
use App\Http\Controllers\EnabledProvidersController;

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

$API_VERSION = env( 'API_VERSION');

Route::group(['as' => 'api.provider.'.$API_VERSION.'.', 'prefix' => 'api/'.$API_VERSION], function(){

    Route::get('available-providers', [AvailableProvidersController::class, 'index' ])->name('available.providers');

    Route::get('enabled-providers'  , [EnabledProvidersController::class, 'index'])->name('enabled.providers');

    Route::get('enabled-provider/{uuid}', [EnabledProvidersController::class, 'provider'])->name('enabled.providers.info');

    Route::get('loginprovider/{uuid}', [AuthenticatedSessionController::class, 'redirectToProvider'])->name('login.providers');

    Route::get('loginprovider/callback/{provider}', [AuthenticatedSessionController::class, 'handleProviderCallBack' ])->name('login.provider.callback');

});