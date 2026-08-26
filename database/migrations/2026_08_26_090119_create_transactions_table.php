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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // income, expense, adjustment
            $table->decimal('amount', 20, 2); // always positive
            $table->string('currency', 3)->default('IDR');
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->string('status')->default('completed'); // completed, pending, voided
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('account_id');
            $table->index('category_id');
            $table->index('transaction_date');
            $table->index(['account_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
