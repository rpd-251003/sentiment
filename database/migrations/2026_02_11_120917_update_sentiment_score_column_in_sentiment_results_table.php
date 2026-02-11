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
        Schema::table('sentiment_results', function (Blueprint $table) {
            // Change sentiment_score from decimal(5,4) to decimal(5,2)
            // This allows values from 0.00 to 999.99, supporting both old (0-10) and new (0-100) rating scales
            $table->decimal('sentiment_score', 5, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sentiment_results', function (Blueprint $table) {
            // Revert back to decimal(5,4)
            $table->decimal('sentiment_score', 5, 4)->change();
        });
    }
};
