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
        Schema::create('budget_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('daily', 12, 2)->nullable();
            $table->decimal('weekly', 12, 2)->nullable();
            $table->decimal('monthly', 12, 2)->nullable();
            $table->decimal('other_monthly', 12, 2)->nullable();
            $table->decimal('yearly', 12, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_lines');
    }
};
