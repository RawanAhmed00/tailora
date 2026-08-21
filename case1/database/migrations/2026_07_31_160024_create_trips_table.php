<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('trips', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('country_id')
        ->constrained('countries')
        ->cascadeOnDelete();

    // Flight will be selected later
    $table->foreignId('flight_id')
        ->nullable()
        ->constrained('flights')
        ->nullOnDelete();

    $table->date('start_date');

    $table->date('end_date');

    $table->decimal('budget', 12, 2);

    $table->string('travel_style');

    $table->json('interests')->nullable();

    $table->unsignedInteger('travelers')->default(1);

    $table->string('source')->default('system');

    $table->string('status')->default('planned');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};