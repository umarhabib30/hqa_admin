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
        Schema::table('general_donations', function (Blueprint $table) {
            if (!Schema::hasColumn('general_donations', 'honor_type')) {
                $table->string('honor_type')->nullable()->after('donation_for');
            }
            if (!Schema::hasColumn('general_donations', 'honor_name')) {
                $table->string('honor_name')->nullable()->after('honor_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_donations', function (Blueprint $table) {
            if (Schema::hasColumn('general_donations', 'honor_name')) {
                $table->dropColumn('honor_name');
            }
            if (Schema::hasColumn('general_donations', 'honor_type')) {
                $table->dropColumn('honor_type');
            }
        });
    }
};

