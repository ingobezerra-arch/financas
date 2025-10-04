<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_code', 10); // Código ISPB do banco
            $table->string('bank_name');
            $table->string('institution_id')->nullable(); // ID da instituição no Open Finance
            $table->string('consent_id')->nullable(); // ID do consentimento
            $table->string('access_token')->nullable(); // Token de acesso (criptografado)
            $table->string('refresh_token')->nullable(); // Token de refresh (criptografado)
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('consent_expires_at')->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'revoked', 'error'])->default('pending');
            $table->json('permissions')->nullable(); // Permissões concedidas
            $table->json('accounts_data')->nullable(); // Dados das contas vinculadas
            $table->timestamp('last_sync_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable(); // Dados adicionais do banco
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'bank_code']);
            $table->index(['status', 'is_active']);
            $table->index(['consent_expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_integrations');
    }
};
