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
        Schema::create('investment_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Reksa Dana Sucorinvest", "BBCA"
            $table->string('symbol')->nullable(); // e.g. "BBCA", "GOTO"
            $table->string('type'); // stock, mutual_fund, crypto, other
            $table->integer('lots')->nullable(); // For stocks: number of lots (1 lot = 100 shares in IDX)
            $table->decimal('units', 20, 8)->default(0); // Shares = lots*100 for stock; NAB units for mutual fund
            $table->decimal('average_buy_price', 20, 2)->default(0); // Per share for stock; per unit for mutual fund
            $table->decimal('current_market_price', 20, 2)->default(0); // Per share for stock; NAB for mutual fund
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_assets');
    }
};
