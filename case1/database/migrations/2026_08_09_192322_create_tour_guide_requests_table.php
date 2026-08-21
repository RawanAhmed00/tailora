<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_guide_requests', function (Blueprint $table) {
            $table->id();

            // Booking
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            // Tour Guide
            // NULL until a tour guide accepts the request
            $table->foreignId('tour_guide_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Fixed price for tour guide service
            $table->decimal('price', 10, 2);

            // Request status
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
            ])->default('pending');

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_guide_requests');
    }
};