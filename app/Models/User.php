<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Traits\HasTokens;

class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
/*
    Estos son los que usa modelo api/account
        
        'firstname',
        'lastname',
        'email',
        'phone_number',
        'company_name',
        'city',
        'biography',
        'headline',
        'department',
        'media_id',
        'upload_folder_id',
        'invitation_id',
        'password',
        'language_id',
        'type'

*/

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Los campos que usan fecha
     * @var [type]
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * Los campos calculados
     * @var array
     */
    protected $appends = [ 'avatar_url'] ;


    /**
     * URL al avatar de usuario
     */
    public function AvatarUrl(): Attribute
    {
        return new Attribute(
            get: fn()=> Storage::disk('avatars')->url('d'.$this->id.'/'.$this->avatar),
        );
    }

    /**
     * Get user by email
     * @param  string $email [description]
     * @return [object or null]        [description]
     */
    public static function getUserByEmail(string $email): ?object
    {
        return User::where('email', '=', $email)->First();
    }

}
