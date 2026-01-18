<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donation_bookings', function (Blueprint $table) {
            $table->id();

            // EVENT INFO
            $table->string('event_title');
            $table->text('event_desc')->nullable();

            // DATE & TIME
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->time('event_start_time');
            $table->time('event_end_time');

            // LOCATION / CONTACT
            $table->string('event_location');
            $table->string('contact_number');

            // SEATING
            $table->integer('total_tables');
            $table->integer('seats_per_table')->default(10);
            $table->integer('total_seats');

            // ✅ FULL TABLE CONFIG (EVENT LEVEL)
            $table->boolean('allow_full_table')->default(false);
            $table->decimal('full_table_price', 10, 2)->nullable();

            // ✅ TRACK HOW MANY FULL TABLES BOOKED
            $table->integer('full_tables_booked')->default(0);

            // BOOKINGS DATA
            $table->json('table_bookings')->nullable();
            $table->json('ticket_types')->nullable();

// STRIPE FIELDS 
    $table->string('stripe_payment_intent_id')->nullable();
    $table->string('payment_status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_bookings');
    }
};
