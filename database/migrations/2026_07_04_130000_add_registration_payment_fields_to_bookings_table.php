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
            $table->decimal('registration_charge', 10, 2)->default(500.00)->after('amount');
            $table->string('registration_payment_status')->default('pending')->after('registration_charge');
            $table->string('registration_payment_id')->nullable()->after('registration_payment_status');
            $table->string('registration_order_id')->nullable()->after('registration_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'registration_charge',
                'registration_payment_status',
                'registration_payment_id',
                'registration_order_id',
            ]);
        });
    }
};
