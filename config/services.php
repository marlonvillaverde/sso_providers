<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],


    /**
     * Estos deberian ser leidos de una base de datos
     */
    'auth0' => [    
      'client_id' => env('AUTH0_CLIENT_ID'),  
      'client_secret' => env('AUTH0_CLIENT_SECRET'),  
      'redirect' => env('AUTH0_REDIRECT_URI'),
      'base_url' => env('AUTH0_BASE_URL'),
    ],

    'azure' => [    
      'client_id' => env('AZURE_CLIENT_ID'),
      'client_secret' => env('AZURE_CLIENT_SECRET'),
      'redirect' => env('AZURE_REDIRECT_URI'),
      'tenant' => env('AZURE_TENANT_ID'),
      'proxy' => env('AZURE_PROXY'),  
    ],

    'saml2' => [
        'metadata' => env('SAML2_SOCIALITE_PROVIDER_METADATA'),
        'acs' => env('SAML2_SOCIALITE_PROVIDER_ACS'),
        'entityid' => env('SAML2_SOCIALITE_PROVIDER_ENTITYID'), 
        'certificate' => env('SAML2_SOCIALITE_PROVIDER_CERTIFICATE'),
        'sp_default_binding_method' => env('SAML2_SP_DEFAULT_BINDING_METHOD'),        
        'sp_acs' => env('SAML2_SP_ACS'),
    ],

    'cognito' => [
        'host' => env('COGNITO_HOST'),
        'client_id' => env('COGNITO_CLIENT_ID'),
        'client_secret' => env('COGNITO_CLIENT_SECRET'),
        'redirect' => env('COGNITO_CALLBACK_URL'),
        'scope' => explode(",", env('COGNITO_LOGIN_SCOPE')),
        'logout_uri' => env('COGNITO_SIGN_OUT_URL')
    ],


    'okta' => [    
      'base_url' => env('OKTA_BASE_URL'),
      'client_id' => env('OKTA_CLIENT_ID'),  
      'client_secret' => env('OKTA_CLIENT_SECRET'),  
      'redirect' => env('OKTA_REDIRECT_URI') 
    ],

];