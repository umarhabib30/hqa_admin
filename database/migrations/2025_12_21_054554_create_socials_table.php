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
        Schema::create('socials', function (Blueprint $table) {
            $table->id();
            $table->string('title');            // e.g., Facebook
            $table->text('desc')->nullable();   // optional description
            $table->string('image')->nullable(); // icon/thumbnail
            $table->string('fblink')->nullable();             // social URL
            $table->string('ytlink')->nullable();             // social URL
            $table->string('instalink')->nullable();             // social URL
            $table->string('tiktoklink')->nullable();             // social URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('socials');
    }
};
