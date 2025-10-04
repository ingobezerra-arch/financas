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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'income', 'expense', 'transfer'
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->text('notes')->nullable();
            $table->date('transaction_date');
            $table->boolean('is_recurring')->default(false);
            $table->foreignId('recurring_transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('completed'); // 'pending', 'completed', 'cancelled'
            $table->json('tags')->nullable();
            $table->string('receipt_url')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
