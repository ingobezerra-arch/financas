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
        Schema::create('synced_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_integration_id')->constrained()->onDelete('cascade');
            $table->string('bank_transaction_id')->unique(); // ID único da transação no banco
            $table->string('account_id'); // ID da conta no banco
            $table->string('account_number'); // Número/identificação da conta
            $table->string('account_type'); // tipo da conta (corrente, poupança, etc)
            
            // Dados da transação
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->string('description');
            $table->text('full_description')->nullable();
            $table->date('transaction_date');
            $table->timestamp('processed_at');
            
            // Categoria e classificação
            $table->string('bank_category')->nullable();
            $table->string('mcc_code')->nullable(); // Merchant Category Code
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            
            // Dados do beneficiário/pagador
            $table->string('counterpart_name')->nullable();
            $table->string('counterpart_document')->nullable();
            $table->string('counterpart_account')->nullable();
            
            // Controle de sincronização
            $table->boolean('is_processed')->default(false);
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->json('raw_data')->nullable(); // dados originais da API
            $table->text('processing_notes')->nullable();
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['user_id', 'transaction_date']);
            $table->index(['bank_integration_id', 'is_processed']);
            $table->index(['account_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('synced_transactions');
    }
};