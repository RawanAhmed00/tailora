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
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'tour_guide_id')) {
                $table->foreignId('tour_guide_id')
                    ->nullable()
                    ->after('trip_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('reviews', 'review_type')) {
                $table->enum('review_type', ['trip', 'tour_guide'])
                    ->default('trip')
                    ->after('tour_guide_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'tour_guide_id')) {
                $table->dropForeign(['tour_guide_id']);
                $table->dropColumn('tour_guide_id');
            }

            if (Schema::hasColumn('reviews', 'review_type')) {
                $table->dropColumn('review_type');
            }
        });
    }
};
