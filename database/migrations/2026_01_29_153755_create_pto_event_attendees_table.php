<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pto_event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('pto_events')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->integer('number_of_guests')->default(0);
            $table->string('profile_pic')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pto_event_attendees');
    }
    

    
};
