<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_travel_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('ai_conversation_id')
                ->nullable()
                ->constrained('ai_conversations')
                ->cascadeOnDelete();

            $table->string('destination');

            $table->unsignedInteger('number_of_days');

            $table->decimal('budget', 12, 2)->nullable();

            $table->unsignedInteger('number_of_travelers')
                ->default(1);

            $table->string('travel_style')->nullable();

            $table->json('interests')->nullable();

            $table->string('plan_name');

            $table->text('description')->nullable();

            /*
             * Cities suggested by AI.
             *
             * Example:
             * [
             *   {"city_id": 90, "days": 2},
             *   {"city_id": 123, "days": 2},
             *   {"city_id": 31, "days": 1}
             * ]
             */
            $table->json('cities')->nullable();

            /*
             * Full AI generated itinerary.
             */
            $table->json('itinerary')->nullable();

            $table->boolean('is_selected')
                ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_travel_plans');
    }
};