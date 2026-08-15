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
        Schema::create('housing_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('horizon_year');
            $table->decimal('sale_price', 14, 2)->nullable();
            $table->decimal('mortgage_on_sold', 14, 2)->nullable();
            $table->decimal('equity_from_sale', 14, 2)->nullable();
            $table->decimal('saving_per_year', 14, 2)->nullable();
            $table->decimal('saved_total', 14, 2)->nullable();
            $table->decimal('expected_income', 14, 2)->nullable();
            $table->decimal('possible_loan', 14, 2)->nullable();
            $table->decimal('student_loan', 14, 2)->nullable();
            $table->decimal('mortgage', 14, 2)->nullable();
            $table->decimal('purchase_price', 14, 2)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'horizon_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housing_plans');
    }
};
