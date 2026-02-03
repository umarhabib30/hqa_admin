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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'permission_id']);
        });

        // Migrate existing role_permissions to user_permissions so current users keep their access
        $users = DB::table('users')->whereIn('role', ['admin', 'manager'])->get();
        foreach ($users as $user) {
            $permissionIds = DB::table('role_permissions')
                ->where('role', $user->role)
                ->pluck('permission_id');
            foreach ($permissionIds as $permissionId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id' => $user->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
