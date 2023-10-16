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
        Schema::create('available_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uuid');
            $table->json('template');
            $table->string('describe');
            $table->enum('sso_type', ['oauth2.0','saml2.0']);            
            $table->timestamps();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_providers');
    }
};