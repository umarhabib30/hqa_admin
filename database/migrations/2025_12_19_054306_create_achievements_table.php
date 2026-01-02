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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            // MAIN INFO
            $table->string('main_title')->nullable();
            $table->text('main_desc')->nullable();

            // CARD INFO
            $table->string('card_title');
            $table->decimal('card_price', 10, 2);
            $table->integer('card_percentage');
            $table->json('card_desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
