<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_fee_person_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('alumni_events')->onDelete('cascade'); // Link to Alumni Events
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_fee_person_prices');
    }
};
