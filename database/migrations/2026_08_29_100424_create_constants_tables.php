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
        Schema::create('category_types', function (Blueprint $table) {
            $table->string('value')->primary();
            $table->string('label');
        });

        Schema::create('account_types', function (Blueprint $table) {
            $table->string('value')->primary();
            $table->string('label');
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->string('value')->primary();
            $table->string('label');
        });

        Schema::create('transaction_types', function (Blueprint $table) {
            $table->string('value')->primary();
            $table->string('label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_types');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('account_types');
        Schema::dropIfExists('category_types');
    }
};
