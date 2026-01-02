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
        Schema::create('alumni_forms', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->year('graduation_year');

            $table->enum('status', ['single', 'married']);

            $table->string('email')->unique();
            $table->string('phone');

            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zipcode');

            $table->string('college');
            $table->string('degree');

            $table->string('company')->nullable();
            $table->string('job_title')->nullable();

            $table->text('achievements')->nullable();

            $table->string('image')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_forms');
    }
};
