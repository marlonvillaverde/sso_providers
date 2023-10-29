<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;


use App\Http\Controllers\AvailableProvidersController;
use App\Http\Controllers\EnabledProvidersController;

use Illuminate\Support\Facades\Route;



Route::group(['as' => 'api.provider.'], function(){

    Route::get('available-providers', [AvailableProvidersController::class, 'index' ])->name('available.providers');

    Route::get('enabled-providers'  , [EnabledProvidersController::class, 'index'])->name('enabled.providers');

      Route::get('enabled-providers-by-company/{uuid}', [EnabledProvidersController::class, 'byCompany' ])->name('enabled.providers.by.company');

    Route::get('enabled-provider/{uuid}', [EnabledProvidersController::class, 'provider'])->name('enabled.providers.info');
});

       


Route::middleware('auth:api')->prefix('/auth')->group(function () {    

    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

    Route::get('user', [AuthenticatedSessionController::class, 'user'])
                ->name('user');
});