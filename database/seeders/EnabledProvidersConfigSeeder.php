<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\EnabledProviderConfig;
use App\Traits\Uuid;
use App\Models\AvailableProvider;


class EnabledProvidersConfigSeeder extends Seeder
{

    use Uuid;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
  
        // https://developer.twitter.com/en/portal/dashboard
        $uuid='49517ac4-d344-4090-b492-a7ebd3c5cdd9';
        $register = EnabledProviderConfig::create([
            'provider_id' => AvailableProvider::FindProvider('twitter')->id,
            'uuid' => $uuid,
            'cfg_template' => json_encode([                
                'client_id' => 'yftm29dJWxAkS4VMRtrqEit9G',
                'client_secret' => 'RCyQqoqPSDGsUSMObXXVtM5fatbV8tVlqWmQO3TU2cI0lCUQrB',                
            ]),
            'cfg_user' => json_encode([
                'id' => 'id',
                'name' => 'name',
                'email' => 'email',
            ]),
            'type' => 'auth2.0',
            'describe' => 'Proveedor de Twitter',
        ]);

        $uuid='542ee85c-23ab-4169-a36d-8fac4d9daaf6';
        $register = EnabledProviderConfig::create([
            'provider_id' => AvailableProvider::FindProvider('facebook')->id,
            'uuid' => $uuid,
            'cfg_template' => json_encode([                
                'client_id' => '1634120374000651',
                'client_secret' => '6ab2e0eb993d1e553537a7a03f3bc571',
                'redirect' => "https://pruebas.com/loginsocial/callback/$uuid",
            ]),
            'cfg_user' => json_encode([
                'id' => 'id',
                'name' => 'name',
                'email' => 'email',
            ]),        
            'type' => 'auth2.0',
            'describe' => 'Proveedor de Facebook',
        ]);

    
        $uuid='8662d561-1b70-472d-918d-c4de1439a793';
        $register = EnabledProviderConfig::create([
            'company_id' => 1,
            'provider_id' => AvailableProvider::FindProvider('google')->id,
            'uuid' => $uuid,
            'cfg_template' => json_encode([                
                'client_id' => '962903568576-enduhflqcpfb026chi3nbl1r7jfpmo0v.apps.googleusercontent.com',
                'client_secret' => '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',
            ]),
            'cfg_user' => json_encode([
                'id' => 'id',
                'name' => 'name',
                'email' => 'email',
            ]),
            'type' => 'auth2.0',
            'describe' => 'Proveedor de Google',
        ]);


        $uuid='5aac6458-aef7-44ac-9982-e18183c20e48';
        $register = EnabledProviderConfig::create([
            'company_id' => 1,
            'provider_id' => AvailableProvider::FindProvider('auth0')->id,
            'uuid' => $uuid,
            'cfg_template' => json_encode([                
                'client_id' => 'RNgpcrTywylP3BCFMhfwGgqDfIC06Yxt',
                'client_secret' => '7ZN5EG9qTobmqFLZk0FfVf-Bq2GMGoUp7HEnVdN2agmkZRpbiwkEVfEOW7_GPpdf',
                'base_url' => 'https://dev-iq0wcsqrwnqgvrey.us.auth0.com',

            ]),
            'cfg_user' => json_encode([
                'camps_id' => [],
                'firstname' => '',
                'lastname'=> '',
                'lang'=> '',
                'username'=> '',                
            ]),        
            'type' => 'auth2.0',
            'describe' => 'Proveedor de Auth0',
        ]);
        

        $uuid=Self::uuid();
        $register = EnabledProviderConfig::create([
            'company_id' => 1,
            'provider_id' => AvailableProvider::FindProvider('auth0')->id,
            'uuid' => $uuid,
            'cfg_template' => json_encode([                
                'client_id' => 'RNgpcrTywylP3BCFMhfwGgqDfIC06Yxt',
                'client_secret' => '7ZN5EG9qTobmqFLZk0FfVf-Bq2GMGoUp7HEnVdN2agmkZRpbiwkEVfEOW7_GPpdf',
                'redirect' => "https://pruebas.com/loginsocial/callback/$uuid",
                'base_url' => 'https://dev-iq0wcsqrwnqgvrey.us.auth0.com',

            ]),
            'cfg_user' => json_encode([
                'camps_id' => [],
                'firstname' => '',
                'lastname'=> '',
                'lang'=> '',
                'username'=> '',                
            ]),        
            'type' => 'auth2.0',
            'describe' => 'Proveedor de Auth0',
        ]);

        

        $uuid=Self::uuid();
        $register = EnabledProviderConfig::create([
            'company_id' => 1,
            'provider_id' => AvailableProvider::FindProvider('auth0')->id,
            'uuid' => $uuid,
            'cfg_template' => json_encode([                
                'client_id' => 'RNgpcrTywylP3BCFMhfwGgqDfIC06Yxt',
                'client_secret' => '7ZN5EG9qTobmqFLZk0FfVf-Bq2GMGoUp7HEnVdN2agmkZRpbiwkEVfEOW7_GPpdf',
                'redirect' => "https://pruebas.com/loginsocial/callback/$uuid",
                'base_url' => 'https://dev-iq0wcsqrwnqgvrey.us.auth0.com',

            ]),
            'cfg_user' => json_encode([
                'camps_id' => [],
                'firstname' => '',
                'lastname'=> '',
                'lang'=> '',
                'username'=> '',                
            ]),        
            'type' => 'auth2.0',
            'describe' => 'Proveedor de Auth0',
        ]);


/*
{"base_url": "https://okta.griky.co", "redirect": "https://pruebas.com/loginsocial/callback/5aac6458-aef7-44ac-9982-e18183c20e48", "client_id": "dcPY7nkcTWcr3ulnJjgl0jHWwpBrCZNb", "client_secret": "JO5XC3fThMmMJTgrQZs1WQp5sodRMNZbSvHH8BdC7tehfnweS6ydD_73xrcWZy_P"}
*/
    }
}
