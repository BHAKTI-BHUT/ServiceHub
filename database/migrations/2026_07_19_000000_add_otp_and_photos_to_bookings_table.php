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
            $table->string('pickup_otp', 10)->nullable()->after('supervisor_acceptance_status');
            $table->text('box_photos')->nullable()->after('pickup_otp');
            $table->string('payment_method', 20)->nullable()->after('box_photos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['pickup_otp', 'box_photos', 'payment_method']);
        });
    }
};
