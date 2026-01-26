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
        Schema::create('sponser_package_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('sponsor_package_id')->nullable();
            $table->string('sponsor_type')->nullable();
            $table->string('status')->default('paid');
            $table->string('image')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponser_package_subscribers');
    }
};
