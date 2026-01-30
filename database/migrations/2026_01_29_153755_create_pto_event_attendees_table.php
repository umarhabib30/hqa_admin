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
        Schema::create('pto_event_attendees', function (Blueprint $table) {
            $table->id(); // primary key
            $table->string('first_name'); // first name of attendee
            $table->string('last_name');  // last name of attendee
            $table->string('email')->unique(); // unique email
            $table->string('phone'); // phone number
            // $table->boolean('will_attend'); // true if attending
            $table->integer('number_of_guests')->default(0); // number of guests
            $table->string('profile_pic')->nullable(); // file path for profile pic
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pto_event_attendees');
    }
};
