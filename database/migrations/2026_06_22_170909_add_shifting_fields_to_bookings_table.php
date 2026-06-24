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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('pickup_contact_name')->nullable()->after('pickup_longitude');
            $table->string('pickup_contact_mobile')->nullable()->after('pickup_contact_name');
            $table->string('drop_contact_name')->nullable()->after('drop_longitude');
            $table->string('drop_contact_mobile')->nullable()->after('drop_contact_name');
            
            $table->decimal('total_distance', 8, 2)->default(0.00)->after('drop_contact_mobile');
            $table->integer('total_volume_score')->default(0)->after('total_distance');
            $table->foreignId('category_id')->nullable()->after('total_volume_score')->constrained('categories')->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->after('category_id')->constrained('vehicles')->onDelete('set null');
            
            $table->decimal('base_fare', 10, 2)->default(0.00)->after('amount');
            $table->decimal('distance_charges', 10, 2)->default(0.00)->after('base_fare');
            $table->decimal('addon_charges', 10, 2)->default(0.00)->after('distance_charges');
            $table->decimal('floor_charges', 10, 2)->default(0.00)->after('addon_charges');
            $table->decimal('weekend_charges', 10, 2)->default(0.00)->after('floor_charges');
            $table->decimal('month_end_charges', 10, 2)->default(0.00)->after('weekend_charges');
            
            $table->decimal('advance_amount', 10, 2)->default(500.00)->after('month_end_charges');
            $table->decimal('remaining_amount', 10, 2)->default(0.00)->after('advance_amount');
            $table->enum('advance_payment_status', ['pending', 'paid'])->default('pending')->after('remaining_amount');
            $table->enum('remaining_payment_status', ['pending', 'paid'])->default('pending')->after('advance_payment_status');
            
            // Note: Since 'status' is already an enum, we will use string for wider compatibility if the list grows.
            $table->string('tracking_status')->default('pending_confirmation')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
