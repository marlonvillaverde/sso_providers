<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordRepository
{
    /**
     * @param string $identifier
     * @param int $account_id
     * @return string
     */
    public static function createToken(string $identifier, int $account_id): string
    {
        $token = hash_hmac('sha256', Str::random(40), config('auth.passwords.key'));

        DB::table('password_resets')->updateOrInsert([
            'identifier' => $identifier,
            'account_id' => $account_id,
        ], [
            'token' => $token,
            'created_at' => now(),
        ]);

        return $token;
    }
}
