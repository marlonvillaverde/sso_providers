<?php

/*
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
*/

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\AvailableProvidersController;
use App\Http\Controllers\EnabledProvidersController;

use Illuminate\Support\Facades\Route;


$API_VERSION = env( 'API_VERSION');


Route::group(['as' => 'api.provider.'.$API_VERSION.'.', 'prefix' => $API_VERSION], function(){

    Route::get('available-providers', [AvailableProvidersController::class, 'index' ])->name('available.providers');

    Route::get('enabled-providers'  , [EnabledProvidersController::class, 'index'])->name('enabled.providers');

    Route::get('enabled-provider/{uuid}', [EnabledProvidersController::class, 'provider'])->name('enabled.providers.info');

    Route::get('loginprovider/{uuid}', [AuthenticatedSessionController::class, 'redirectToProvider'])->name('login.providers');

    Route::get('loginprovider/callback/{provider}', [AuthenticatedSessionController::class, 'handleProviderCallBack' ])->name('login.provider.callback');

});

       


Route::middleware('auth:api')->prefix($API_VERSION.'/auth')->group(function () {
/*
    Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');

    

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

  */ 
 

    
    

    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    Route::get('user', [AuthenticatedSessionController::class, 'user'])
                ->name('user');
});