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
            $table->foreignId('item_size_id')->nullable()->after('item_name')->constrained('item_sizes')->nullOnDelete();
            $table->dropColumn('volume_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['item_size_id']);
            $table->dropColumn('item_size_id');
            $table->integer('volume_score')->default(1)->after('item_name');
        });
    }
};
