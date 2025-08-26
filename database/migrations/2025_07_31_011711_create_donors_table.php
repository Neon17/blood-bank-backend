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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Personal Information
            $table->string('contact_number')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('address')->nullable();
            $table->date('date_of_birth')->nullable();

            // Medical History
            $table->string('weight')->nullable();
            $table->string('height')->nullable(); // in cm
            $table->date('last_donated_date')->nullable();
            $table->string('medical_conditions')->nullable();
            $table->string('current_medication')->nullable();
            $table->string('current_health_status')->nullable();

            // Contact Location
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('country')->default('Nepal')->nullable();
            $table->string('city')->default('Kathmandu')->nullable();

            // Verification (NULL means pending)
            $table->enum('verification_status', ['pending', 'approved', 'wrong'])->nullable();
            $table->string('admin_message')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
