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
        Schema::create('top_achievers', function (Blueprint $table) {
            $table->id();
           $table->string('title');
            $table->text('desc')->nullable();
            $table->string('image')->nullable();
            $table->string('class_achiever');
            $table->string('achiever_name');
            $table->text('achiever_desc')->nullable();

            // New: Paired meta title + image
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_achievers');
    }
};
