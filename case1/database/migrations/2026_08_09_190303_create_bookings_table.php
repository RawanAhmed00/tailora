<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Customer
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Flight
            $table->foreignId('flight_id')
                ->nullable()
                ->constrained('flights')
                ->nullOnDelete();

            // Hotel
            $table->foreignId('hotel_id')
                ->nullable()
                ->constrained('hotels')
                ->nullOnDelete();

            // Pricing
            $table->decimal('total_price', 10, 2);

            // Hotel stay
            $table->unsignedInteger('number_of_nights')
                ->nullable();

            // Booking status
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed',
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};