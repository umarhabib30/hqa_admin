<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_event_attendees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained('alumni_events')
                ->cascadeOnDelete();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');

            $table->integer('number_of_guests')->default(0);

            $table->decimal('amount', 10, 2);
            $table->string('payment_id');

            $table->string('profile_pic')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_event_attendees');
    }
};
