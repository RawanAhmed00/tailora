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
    Schema::create('hotels', function (Blueprint $table) {
        $table->id();

        $table->string('name');
        $table->string('city');
        $table->string('neighborhood')->nullable();

        $table->decimal('distance_km', 8, 2)->nullable();

        $table->unsignedInteger('price_per_night');

        $table->decimal('rating', 3, 2)->default(0);

        $table->unsignedInteger('review_count')->default(0);

        $table->text('amenities')->nullable();

        $table->unsignedInteger('available_rooms')->default(0);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
