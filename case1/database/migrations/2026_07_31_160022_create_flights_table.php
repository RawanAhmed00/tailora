
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id') ->constrained('users') ->cascadeOnDelete();
            

            // Ignav
            $table->string('ignav_id')->nullable()->index();

            // Route
            $table->string('origin', 3);
            $table->string('destination', 3);

            // Dates
            $table->date('departure_date');
            $table->date('return_date')->nullable();

            // Outbound flight
            $table->string('outbound_carrier_code', 2)->nullable();
            $table->string('outbound_flight_number')->nullable();

            // Inbound flight
            $table->string('inbound_carrier_code', 2)->nullable();
            $table->string('inbound_flight_number')->nullable();

            // Airline
            $table->string('airline')->nullable();

            // Price
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('USD');

            // Booking
            $table->text('booking_url')->nullable();

            $table->timestamps();

            $table->index([
                'origin',
                'destination',
                'departure_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};