<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\EnabledProviderConfig;
use App\Models\ProviderLogin;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


use Reflection;
use ReflectionClass;

use Illuminate\Http\RedirectResponse;


class AuthenticatedSessionController extends Controller
{


    const SSO_SAML_TYPE = 'saml2.0';
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): Response
    {
         $request->validate([
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $credentials =  request(['email', 'password']);
        //$credentials['active'] = 1;
        $credentials['deleted_at'] = null;

        if (!Auth::attempt($credentials)) {

            return response(json_encode([
                'message' => 'Unauthorized']), 401);
        }


        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return response(json_encode([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at)
                    ->toDateTimeString(),
            ], 200));
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {

        $request->user()->token()->revoke();

        return response(json_encode(['message' => 'Successfully logged out']),200);
    }


    /**
     * Get the request user
     */
     public function user(Request $request): Response
     {
        return response(json_encode( ['user' => $request->user()]), 200);
    }


    /**
     * Funciones para uso de socialite
     */
    
    private function getProviderConfig(string $provider) : array
    {


        $enabledProvider = EnabledProviderConfig::FindByUuid($provider);

        if (!$enabledProvider) {
            return [
                'message' => 'No se consiguieron datos del provider' ,
                'status' => '404'
            ];
        }

        $cfgProvider = json_decode($enabledProvider->cfg_template,true);
        
        if ($enabledProvider->sso_type == Self::SSO_SAML_TYPE)
        {
            $describe = "saml2";
            $cfgProvider = json_decode($enabledProvider->cfg_template,true);
            
            
            $auxCfg = [];
            $additionalProvidersConfig = $cfgProvider;
            foreach( $additionalProvidersConfig as $clave => $valor ){
                $auxCfg[$clave]=$valor['value'];
            }

            $config = new \SocialiteProviders\Manager\Config(null, null, null, $additionalProvidersConfig);
            
        }
        else{
            $describe = $enabledProvider->provider->name;
            $clientId = $cfgProvider['client_id']['value'];
            $clientSecret = $cfgProvider['client_secret']['value'];
            $callbackUrl = env('LOGIN_PROVIDER_URL').'callback/'.$provider;

            //$cfgProvider['redirect'];
            $auxCfg = [];
            $additionalProvidersConfig = Arr::except($cfgProvider, ['client_id', 'client_secret', 'redirect']);
            foreach( $additionalProvidersConfig as $clave => $valor ){
                $auxCfg[$clave]=$valor['value'];
            }
            $config = new \SocialiteProviders\Manager\Config($clientId, $clientSecret, $callbackUrl, $auxCfg);
        }

        if (!$config) {
            return [
                'message' => 'No se consiguieron datos de configuracion del provider' ,
                'status' => '404' 
            ];
        }

        return [
            'config' => $config,
            'describe' => $describe,
            'status' => '200'
        ];

    }


    public function redirectToProvider(Request $request, string $provider)
    {

        $providerConfig = Self::getProviderConfig($provider);

        if ($providerConfig['status'] == '404' )
        {
            return response(json_encode($providerConfig['message']),404);
        }
            
        
        //$var = Socialite::driver($providerConfig['describe'])->setConfig($providerConfig['config'])->redirect();
        //$reflex = new ReflectionClass($var);
        //$prop=$reflex->getProperty('targetUrl');
        //$prop->setAccessible(true);
        //$prop->getValue($var,$config);
        
    
       //return Socialite::driver($providerConfig['describe'])->setConfig($providerConfig['config'])->redirect();

       return Socialite::driver($providerConfig['describe'])->setConfig($providerConfig['config'])->redirect();        

    }


    public function getProviderMetadata(Request $request)
    {
        return Socialite::driver('saml2')->getServiceProviderMetadata();
    }


    /**
     * Devolver datos de usuario autenticado por provider
     */
    
    public function handleProviderCallBack(Request $request,  $provider)
    {

        $providerConfig = Self::getProviderConfig($provider);
        if ($providerConfig['status'] == '404' )
        {
            return response(json_encode($providerConfig['message']),404);
        }

        $describeProvider = $providerConfig['describe'];
        $configProvider = $providerConfig['config'];        

        $socialiteProvider = Socialite::driver($providerConfig['describe'])->setConfig($configProvider);
        
        $userSocialite = $socialiteProvider->user();

        $user = User::getUserByEmail($userSocialite->email);

        if ( is_null($user) )
        {            
            $user = User::create([
                'name' => $userSocialite->name,
                'avatar' => $userSocialite->avatar,
                'email' => $userSocialite->email,
                'password' => bcrypt(Str::random(10)),
            ]);
        }

        $userProvider = ProviderLogin::where('user_id','=',$user->id)
                    ->where('provider_id','=', $provider)->first();
                    
        if( $userProvider ){
            $userProvider->user_provider_id = $userSocialite->id;
            $userProvider->token = $userSocialite->token;
            $userProvider->save();
        }
        else{

            try{

                ProviderLogin::create([
                    'user_provider_id' => $userSocialite->id,
                    'provider_id' => $provider,
                    'token' => $userSocialite->token,
                    'user_id' =>$user->id,
                ]);              

            } catch (\Exception $error) {

                return response()->json(['Error al crear el nuevo usuario. ' =>
                                            [
                                                'error' => $error->getCode(),
                                                'message' => $error->getMessage(),
                                            ]
                                        ], 500);
            }
        }

        return $this->loginAndRedirect($user, $request);
  
    }
    


    private function loginAndRedirect(User $user, Request $request)
    {

        [$access_token, $refresh_token] = $user->createTokens();

        $payload = [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token
        ];


        $urlDestino = Env( 'REDIRECT_PROVIDER_URL' ).
            '?culture='.$this->generate_jwt( $payload, 'NUESTRACLAVESUPERSECRETA');

        //$headers = ['X-CODE-SIGN' => $this->generate_jwt( $payload, 'NUESTRACLAVESUPERSECRETA')];
        
        return response()->redirectTo($urlDestino);

    }



    private function generate_jwt($payload, $secret)
    {

        $headers = [

            "alg" => "HS256",

            "typ" => "JWT"

        ];

        $headers_encoded = $this->base64url_encode(json_encode($headers));

        $payload_encoded = $this->base64url_encode(json_encode($payload));

        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);

        $signature_encoded = $this->base64url_encode($signature);

        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
        return $jwt;

    }

 

    private function base64url_encode($str)
    {

        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

}

