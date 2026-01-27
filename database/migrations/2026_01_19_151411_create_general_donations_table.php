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
        Schema::create('general_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_raisa_id')->nullable()->constrained('fund_raisas')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_id')->nullable(); // remove unique() constraint
            $table->string('frequency')->default('once'); // once, month, year
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('status')->default('active');


            $table->enum('donation_mode', ['paid_now', 'pledged', 'stripe'])->nullable()->default('paid_now');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_donations');
    }
};
