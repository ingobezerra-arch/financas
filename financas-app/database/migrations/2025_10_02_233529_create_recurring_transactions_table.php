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
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'income', 'expense'
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->text('notes')->nullable();
            $table->string('frequency'); // 'daily', 'weekly', 'monthly', 'yearly'
            $table->integer('interval')->default(1); // Every X days/weeks/months/years
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_due_date');
            $table->integer('occurrences')->nullable(); // Number of times to repeat
            $table->integer('occurrences_count')->default(0); // Times already executed
            $table->boolean('is_active')->default(true);
            $table->json('tags')->nullable();
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
        Schema::dropIfExists('recurring_transactions');
    }
};
