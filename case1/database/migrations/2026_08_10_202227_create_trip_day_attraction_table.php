<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_day_attraction', function (Blueprint $table) {

            $table->id();

            $table->foreignId('trip_day_id')
                ->constrained('trip_days')
                ->cascadeOnDelete();

            $table->foreignId('attraction_id')
                ->constrained('attractions')
                ->cascadeOnDelete();

            $table->unsignedInteger('order')
                ->default(1);

            $table->timestamps();

            $table->unique([
                'trip_day_id',
                'attraction_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_day_attraction');
    }
};
