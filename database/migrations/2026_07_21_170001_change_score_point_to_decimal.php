<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change all score/volume columns to decimal so they support values like 2.5
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('score_point', 8, 2)->default(0)->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('total_volume_score', 10, 2)->default(0)->change();
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->decimal('calculated_volume_score', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('score_point')->default(0)->change();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('total_volume_score')->default(0)->change();
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->integer('calculated_volume_score')->default(0)->change();
        });
    }
};
