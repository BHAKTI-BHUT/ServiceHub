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
        Schema::table('booking_requests', function (Blueprint $table) {
            // Contact phone number for the booking (may differ from login number)
            $table->string('phone_number', 15)->nullable()->after('customer_id');

            // Make shifting_time optional (app may not show time picker)
            $table->time('shifting_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->time('shifting_time')->nullable(false)->change();
        });
    }
};
