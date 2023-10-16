<?php

namespace App\Http\Controllers\Auth;

use Laravolt\Avatar\Facade as Avatar;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage ;
use Illuminate\Validation\Rules;
use PhpParser\Node\Expr\Cast\Bool_;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'activation_token'  => Hash::make($request->password),
        ]);

        $user->save();
        
        $avatar = Avatar::create($user->name)->getImageObject()->encode('png');

        $this->createUserAvatar($user->id, $avatar);
        
        event(new Registered($user));       

        return response(json_encode([
            'message' => 'Successfully created user!']), 201);

    }


    protected function createUserAvatar( int $userId, string $dataFile ): bool
    {   
        $disk = 'avatars';
        
        $ruta = 'd'.$userId;
        
        if (! Storage::disk($disk)->exists($ruta)) {
                Storage::disk($disk)->makeDirectory($ruta, 0775, true);
        }
        
        return Storage::disk($disk)->put($ruta.'/avatar.png', (string) $dataFile);
        
    }
    
        
}