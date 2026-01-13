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
            // Add individual scores for all 3 sentiments
            $table->decimal('positive_score', 5, 4)->after('sentiment_score')->default(0);
            $table->decimal('negative_score', 5, 4)->after('positive_score')->default(0);
            $table->decimal('neutral_score', 5, 4)->after('negative_score')->default(0);

            // Add comment_type to differentiate between 'nilai' and 'masukan'
            $table->enum('comment_type', ['nilai', 'masukan'])->after('kp_evaluation_id')->default('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sentiment_results', function (Blueprint $table) {
            $table->dropColumn(['positive_score', 'negative_score', 'neutral_score', 'comment_type']);
        });
    }
};
