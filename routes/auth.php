<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;


use App\Http\Controllers\AvailableProvidersController;
use App\Http\Controllers\EnabledProvidersController;

use Illuminate\Support\Facades\Route;


$API_VERSION = env( 'API_VERSION');


Route::group(['as' => 'api.provider.'.$API_VERSION.'.', 'prefix' => $API_VERSION], function(){

  
    Route::get('enabled-providers-by-company/{uuid}', [EnabledProvidersController::class, 'byCompany' ])->name('enabled.providers.by.company');

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