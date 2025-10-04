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
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nome do plano
            $table->text('description')->nullable();
            $table->enum('strategy', ['snowball', 'avalanche', 'custom']); // Estratégia: Bola de Neve, Avalanche ou Personalizada
            $table->decimal('total_debt_amount', 12, 2); // Valor total das dívidas no plano
            $table->decimal('monthly_budget', 10, 2); // Orçamento mensal disponível para pagamento
            $table->decimal('extra_payment', 10, 2)->default(0); // Valor extra para acelerar pagamentos
            $table->date('start_date'); // Data de início do plano
            $table->date('projected_end_date')->nullable(); // Data prevista para quitação
            $table->date('actual_end_date')->nullable(); // Data real de quitação
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->decimal('total_interest_saved', 10, 2)->default(0); // Juros economizados
            $table->integer('months_saved')->default(0); // Meses economizados
            $table->json('strategy_config')->nullable(); // Configurações específicas da estratégia
            $table->json('progress_data')->nullable(); // Dados de progresso
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
        Schema::dropIfExists('payment_plans');
    }
};
