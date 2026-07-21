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
            $table->unsignedBigInteger('vendor_id')->nullable()->after('customer_id');
            $table->decimal('vendor_commission_amount', 10, 2)->default(0)->after('remaining_amount');
            $table->decimal('vendor_settlement_amount', 10, 2)->default(0)->after('vendor_commission_amount');

            // Set up foreign key if we use users table for vendors
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'vendor_commission_amount', 'vendor_settlement_amount']);
        });
    }
};
