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
        if (! Schema::hasColumn('general_donations', 'donation_mode')) {
            Schema::table('general_donations', function (Blueprint $table) {
                $table->enum('donation_mode', ['paid_now', 'pledged'])->nullable()->default('paid_now');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('general_donations', 'donation_mode')) {
            Schema::table('general_donations', function (Blueprint $table) {
                $table->dropColumn('donation_mode');
            });
        }
    }
};

