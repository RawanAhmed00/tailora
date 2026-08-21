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
        Schema::create('restaurants', function (Blueprint $table) {
                $table->id();

    $table->string('name');
    $table->string('city');
    $table->string('address');
    $table->string('locality')->nullable();

    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();

    $table->text('cuisines')->nullable();

    $table->unsignedInteger('average_cost_for_two')->default(0);

    $table->string('currency')->nullable();

    $table->boolean('has_table_booking')->default(false);
    $table->boolean('has_online_delivery')->default(false);
    $table->boolean('is_delivering_now')->default(false);

    $table->unsignedTinyInteger('price_range')->default(1);

    $table->decimal('rating', 3, 2)->default(0);

    $table->unsignedInteger('votes')->default(0);

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
