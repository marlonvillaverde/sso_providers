<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\AvailableProvidersController;
use App\Http\Controllers\EnabledProvidersController;
use PhpParser\Node\Expr\PostDec;

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


Route::group(['as' => 'api.provider.', 'prefix' => 'api/'], function(){

    Route::get('loginprovider/{uuid}', [AuthenticatedSessionController::class, 'redirectToProvider'])->name('login.providers');

    Route::get('loginprovider/callback/{provider}', [AuthenticatedSessionController::class, 'handleProviderCallBack' ])->name('login.provider.callback');

});