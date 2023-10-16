<?php

namespace App\Traits;

use DateTimeImmutable;
use Exception;
use Illuminate\Support\Str;
use JsonException;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\CryptTrait;

trait HasTokens
{
    use CryptTrait;

    /**
     * @return array
     * @throws JsonException
     */
    public function createTokens(): array
    {
        $this->setEncryptionKey(app('encrypter')->getKey());

        $access_token = Token::create([
            'id' => $this->createHash(),
            'user_id' => $this->id,
            'client_id' => config('sso.passport_client_id'),
            'name' => null,
            'scopes' => '[]',
            'revoked' => 0,
            'expires_at' => now()->addSeconds(config('auth.expiration_times.access_token')),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $refresh_token = RefreshToken::create([
            'id' => $this->createHash(),
            'access_token_id' => $access_token->id,
            'revoked' => 0,
            'expires_at' => now()->addSeconds(config('auth.expiration_times.refresh_token'))
        ]);

        $jwt_access_token = $this->getJwtAccessToken($access_token);
        $jwt_refresh_token = $this->getJwtRefreshToken($access_token, $refresh_token);

        return [$jwt_access_token, $jwt_refresh_token];
    }

    /**
     * @return void
     */
    public function deleteTokens(): void
    {
        Token::where('user_id', $this->id)->delete();
    }

    /**
     * @param Token $access_token
     * @return string
     * @throws Exception
     */
    private function getJwtAccessToken(Token $access_token): string
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::file(storage_path('oauth-private.key'))
        );

        return $config
            ->builder()
            ->permittedFor(config('sso.passport_client_id'))
            ->identifiedBy($access_token->id)
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt(new DateTimeImmutable($access_token->expires_at->toDateTimeString()))
            ->relatedTo($this->id)
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }

    /**
     * @param Token $access_token
     * @param RefreshToken $refresh_token
     * @return string
     * @throws JsonException
     */
    private function getJwtRefreshToken(Token $access_token, RefreshToken $refresh_token): string
    {
        $payload = json_encode([
            'client_id' => config('sso.passport_client_id'),
            'refresh_token_id' => $refresh_token->id,
            'access_token_id' => $access_token->id,
            'scopes' => [],
            'user_id' => $this->id,
            'expire_time' => $refresh_token->expires_at->timestamp,
        ], JSON_THROW_ON_ERROR);

        return $this->encrypt($payload);
    }

    /**
     * @return string
     */
    private static function createHash(): string
    {
        return substr(hash('sha512', Str::uuid()), 0, 80);
    }
}
