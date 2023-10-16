<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enabled_providers_config', function (Blueprint $table) {
            $table->id();
            $table->int('company_id');
            $table->string('provider_id');
            $table->string('uuid');
            $table->string('describe');
            $table->json('cfg_template');
            $table->json('cfg_user');
            $table->enum('sso_type', ['oauth2.0','saml2.0']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enabled_providers_config');
    }
};
