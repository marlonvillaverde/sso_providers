<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('enabled_providers_config', function (Blueprint $table) {            
            $table->enum('sso_type', ['cliente', 'proveedor', 'empleado', 'oauth2.0','saml2.0',
        'transportadora', 'nuevo_valor'])->nullable()->change(); 
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enabled_providers_config', function (Blueprint $table) {
            //
        });
    }
};
