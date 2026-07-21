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
        Schema::table('items', function (Blueprint $table) {
            $table->integer('score_point')->default(0)->after('item_size_id');
        });

        Schema::table('item_sizes', function (Blueprint $table) {
            $table->integer('volume_score')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('score_point');
        });

        Schema::table('item_sizes', function (Blueprint $table) {
            $table->integer('volume_score')->nullable(false)->change();
        });
    }
};
