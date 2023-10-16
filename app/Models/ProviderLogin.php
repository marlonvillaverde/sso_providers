<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderLogin extends Model
{

     protected $table = 'providers_logins';

    protected $fillable = [
        'user_provider_id',
        'provider_id',
        'token',
        'user_id',
    ];


    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
