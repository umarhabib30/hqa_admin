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
        if (! Schema::hasColumn('general_donations', 'fund_raisa_id')) {
            Schema::table('general_donations', function (Blueprint $table) {
                $table->foreignId('fund_raisa_id')->nullable()->constrained('fund_raisas')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('general_donations', 'fund_raisa_id')) {
            Schema::table('general_donations', function (Blueprint $table) {
                $table->dropForeign(['fund_raisa_id']);
                $table->dropColumn('fund_raisa_id');
            });
        }
    }
};

