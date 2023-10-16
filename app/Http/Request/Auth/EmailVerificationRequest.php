<?php

namespace App\Http\Request\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest as ExtendEmailVerification;
use App\Models\User;

class EmailVerificationRequest extends ExtendEmailVerification
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public $user;

    public function authorize()
    {

        $user = User::where('id', $this->route('id'))->first();

        if (! $user) {
            return false;
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $this->route('hash'))){            
                    
            return false;
        }   
        $this->user = $user;
        
        return true;
    }
}
