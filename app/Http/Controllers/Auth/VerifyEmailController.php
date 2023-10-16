<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use App\Http\Request\Auth\EmailVerificationRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\User;


class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        
        if ($request->user->hasVerifiedEmail()) {
            return $this->userIsValid($request);            
        }

        if ($request->user->markEmailAsVerified()) {
            event(new Verified($request->user));
        }
        
        return $this->userIsValid($request);            
    }

    private function userIsValid(Request $request): Response
    {
        return response(json_encode([
                'user' => $request->user,
                'message' => 'Correo verificado',
            ]),200);
    }
}