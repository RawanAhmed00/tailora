<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);

            $table->string('currency', 3)->default('EGP');

            $table->string('status')->default('pending');

            $table->string('payment_method')->nullable();

            $table->string('paymob_order_id')->nullable();

            $table->string('paymob_transaction_id')->nullable();

            $table->string('paymob_intention_id')->nullable();

            $table->text('payment_url')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('paymob_order_id');
            $table->index('paymob_transaction_id');
            $table->index('paymob_intention_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};