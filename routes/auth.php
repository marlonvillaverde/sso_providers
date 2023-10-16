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


$apiVersion = 'v1';


Route::middleware('guest')->prefix($apiVersion)->group( function () {

    /**
     * Rutas para autenticacion de usuarios
     */
    Route::prefix('auth')->group( function(){

        Route::post('register', [RegisteredUserController::class, 'store']);

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                    ->name('verification.verify');
    
    });


    
    

/*

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');


    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
                */
               

    /**
     * Rutas para autenticacion de provider 
     */
    Route::get('available-providers', [AvailableProvidersController::class, 'index']);

    Route::get('enabled-providers', [EnabledProvidersController::class, 'index']);

    Route::get('enabled-provider/{uuid}', [EnabledProvidersController::class, 'provider']);

//    Route::get('login/provider/{uuid}', [AuthenticatedSessionController::class, 'redirectToProvider']);


    Route::get('loginsocial/{uuid}', [AuthenticatedSessionController::class, 'redirectToProvider'])->name('login.social');

    Route::get('loginsocial/callback/{provider}', [AuthenticatedSessionController::class, 'handleProviderCallBack'])
                ->name('login.social.callback');


});


Route::middleware('auth:api')->prefix($apiVersion.'/auth')->group(function () {
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