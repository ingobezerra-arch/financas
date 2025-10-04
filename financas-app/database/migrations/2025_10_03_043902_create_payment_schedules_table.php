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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('debt_id')->constrained()->onDelete('cascade');
            $table->integer('month'); // Mês do cronograma (1, 2, 3...)
            $table->date('due_date'); // Data de vencimento do pagamento
            $table->decimal('payment_amount', 10, 2); // Valor do pagamento
            $table->decimal('minimum_payment', 10, 2); // Pagamento mínimo
            $table->decimal('extra_payment', 10, 2)->default(0); // Pagamento extra
            $table->decimal('interest_amount', 10, 2)->default(0); // Valor dos juros
            $table->decimal('principal_amount', 10, 2); // Valor do principal
            $table->decimal('remaining_balance', 10, 2); // Saldo remanescente após pagamento
            $table->enum('status', ['pending', 'paid', 'overdue', 'skipped'])->default('pending');
            $table->date('paid_date')->nullable(); // Data real do pagamento
            $table->decimal('paid_amount', 10, 2)->nullable(); // Valor realmente pago
            $table->text('notes')->nullable(); // Observações
            $table->integer('priority_order')->default(1); // Ordem de prioridade no mês
            $table->boolean('is_final_payment')->default(false); // É o último pagamento da dívida
            $table->timestamps();
            
            // Índices para melhor performance
            $table->index(['payment_plan_id', 'month']);
            $table->index(['debt_id', 'due_date']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_schedules');
    }
};
