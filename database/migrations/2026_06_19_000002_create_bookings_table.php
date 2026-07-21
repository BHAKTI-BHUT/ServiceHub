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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_request_id')->nullable()->constrained('booking_requests')->onDelete('set null');
            $table->string('pickup_location');
            $table->string('drop_location');
            $table->double('pickup_latitude')->nullable();
            $table->double('pickup_longitude')->nullable();
            $table->double('drop_latitude')->nullable();
            $table->double('drop_longitude')->nullable();
            $table->date('shifting_date');
            $table->time('shifting_time');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
