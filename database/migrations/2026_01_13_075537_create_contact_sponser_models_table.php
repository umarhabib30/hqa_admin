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
        Schema::create('contact_sponser_models', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('sponsor_type')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_sponser_models');
    }
};
