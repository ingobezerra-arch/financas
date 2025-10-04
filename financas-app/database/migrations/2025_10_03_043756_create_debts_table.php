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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nome da dívida
            $table->text('description')->nullable(); // Descrição
            $table->decimal('original_amount', 10, 2); // Valor original da dívida
            $table->decimal('current_balance', 10, 2); // Saldo atual devedor
            $table->decimal('interest_rate', 5, 2)->default(0); // Taxa de juros (% ao mês)
            $table->decimal('minimum_payment', 10, 2); // Pagamento mínimo
            $table->date('due_date')->nullable(); // Data de vencimento
            $table->enum('status', ['active', 'paid', 'overdue', 'negotiated'])->default('active');
            $table->enum('debt_type', ['credit_card', 'loan', 'financing', 'invoice', 'other'])->default('other');
            $table->string('creditor')->nullable(); // Credor/Instituição
            $table->integer('installments_total')->nullable(); // Total de parcelas (se aplicável)
            $table->integer('installments_paid')->default(0); // Parcelas pagas
            $table->date('contract_date')->nullable(); // Data do contrato
            $table->json('additional_info')->nullable(); // Informações adicionais
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debts');
    }
};
