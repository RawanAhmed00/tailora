<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_days', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trip_id')
                ->constrained('trips')
                ->cascadeOnDelete();

          $table->foreignId('city_id')
    ->nullable()
    ->constrained('cities')
    ->nullOnDelete();
            $table->unsignedInteger('day_number');

            $table->date('date');

            $table->decimal('estimated_expenses', 12, 2)
                ->default(0);

            $table->text('transportation_tips')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'trip_id',
                'day_number'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_days');
    }
};