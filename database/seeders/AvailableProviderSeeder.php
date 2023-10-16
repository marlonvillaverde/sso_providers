<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\AvailableProvider;
use App\Traits\Uuid;


class AvailableProviderSeeder extends Seeder
{
    use Uuid;

    public function run()
    {
        $register = AvailableProvider::create([
            'name' => 'twitter',
            'uuid' => '3442e7be-a808-4856-bd18-ef344286cfc5',
            'template' => json_encode([                
                'client_id' => '',
                'client_secret' => '',                               
            ]),            
            'describe' => 'Proveedor de Twitter',
        ]);

        $register = AvailableProvider::create([
            'name' => 'google',
            'uuid' => 'f04341f3-8a5a-48ee-8887-2ce60f7ebd84',
            'template' => json_encode([                
                'client_id' => '',                
                'client_secret' => '',                
            ]),            
            'describe' => 'Proveedor de Google',
        ]);

        $register = AvailableProvider::create([
            'name' => 'facebook',
            'uuid' => 'd031b745-82a4-46c9-b83a-bc1ed0d47a73',
            'template' => json_encode([                
                'client_id' => '',                
                'client_secret' => '',                
            ]),            
            'describe' => 'Proveedor de Facebook',
        ]);


        $register = AvailableProvider::create([
            'name' => 'auth0',
            'uuid' => 'b163e163-4f7d-452f-a40d-ab915f036ec4',
            'template' => json_encode([                
                'client_id' => '',                
                'client_secret' => '',                
                'base_url' => '',
            ]),
            'describe' => 'Proveedor de Auth0',
        ]);
    }
}