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
        Schema::table('kp_evaluations', function (Blueprint $table) {
            // Add new comment_masukan field after rating
            $table->text('comment_masukan')->after('rating');
        });

        // Rename in a separate statement to avoid column reference issues
        Schema::table('kp_evaluations', function (Blueprint $table) {
            // Rename comment_text to comment_nilai for clarity
            $table->renameColumn('comment_text', 'comment_nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kp_evaluations', function (Blueprint $table) {
            // Remove comment_masukan
            $table->dropColumn('comment_masukan');

            // Rename back to comment_text
            $table->renameColumn('comment_nilai', 'comment_text');
        });
    }
};
