<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_donations', function (Blueprint $table) {
            if (!Schema::hasColumn('general_donations', 'other_purpose')) {
                $table->string('other_purpose')->nullable()->after('honor_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_donations', function (Blueprint $table) {
            if (Schema::hasColumn('general_donations', 'other_purpose')) {
                $table->dropColumn('other_purpose');
            }
        });
    }
};

