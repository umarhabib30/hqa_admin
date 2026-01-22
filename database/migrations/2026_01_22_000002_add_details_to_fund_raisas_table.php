<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fund_raisas', function (Blueprint $table) {
            if (! Schema::hasColumn('fund_raisas', 'goal_name')) {
                $table->string('goal_name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('fund_raisas', 'start_date')) {
                $table->date('start_date')->nullable()->after('goal_name');
            }

            if (! Schema::hasColumn('fund_raisas', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });

        // Keep all fields optional (and keep total_donors but do not require it in forms)
        // Using raw SQL to avoid requiring doctrine/dbal for ->change().
        if (Schema::hasColumn('fund_raisas', 'starting_goal')) {
            DB::statement('ALTER TABLE `fund_raisas` MODIFY `starting_goal` INT NULL');
        }
        if (Schema::hasColumn('fund_raisas', 'ending_goal')) {
            DB::statement('ALTER TABLE `fund_raisas` MODIFY `ending_goal` INT NULL');
        }
        if (Schema::hasColumn('fund_raisas', 'total_donors')) {
            DB::statement('ALTER TABLE `fund_raisas` MODIFY `total_donors` INT NOT NULL DEFAULT 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('fund_raisas', 'starting_goal')) {
            DB::statement('ALTER TABLE `fund_raisas` MODIFY `starting_goal` INT NOT NULL');
        }
        if (Schema::hasColumn('fund_raisas', 'ending_goal')) {
            DB::statement('ALTER TABLE `fund_raisas` MODIFY `ending_goal` INT NOT NULL');
        }
        if (Schema::hasColumn('fund_raisas', 'total_donors')) {
            DB::statement('ALTER TABLE `fund_raisas` MODIFY `total_donors` INT NOT NULL');
        }

        Schema::table('fund_raisas', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['goal_name', 'start_date', 'end_date'] as $col) {
                if (Schema::hasColumn('fund_raisas', $col)) {
                    $dropColumns[] = $col;
                }
            }

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};

