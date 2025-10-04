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
        Schema::create('debt_payment_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_plan_id')->constrained()->onDelete('cascade');
            $table->decimal('initial_balance', 10, 2); // Saldo inicial quando adicionado ao plano
            $table->integer('priority_order'); // Ordem de prioridade no plano
            $table->decimal('allocated_extra_payment', 10, 2)->default(0); // Valor extra alocado
            $table->date('added_date'); // Data que foi adicionada ao plano
            $table->date('projected_payoff_date')->nullable(); // Data prevista de quitação
            $table->date('actual_payoff_date')->nullable(); // Data real de quitação
            $table->enum('status', ['active', 'paid', 'removed'])->default('active');
            $table->timestamps();
            
            // Índice único para evitar duplicatas
            $table->unique(['debt_id', 'payment_plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debt_payment_plan');
    }
};
