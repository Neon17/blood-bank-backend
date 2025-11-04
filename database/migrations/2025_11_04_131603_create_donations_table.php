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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('blood_group');
            $table->date('date_time');
            $table->string('exact_location');
            $table->string('contact_number');
            $table->string('contact_name');
            $table->string('contact_email')->nullable(); // to connect with user in the future
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('country');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'cancelled'])->default('pending');


            $table->unsignedBigInteger('blood_request_id')->nullable();
            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('blood_bank_id')->nullable();
            $table->foreign('blood_bank_id')->references('id')->on('blood_banks')->onDelete('cascade');

            $table->unsignedBigInteger('donation_program_id')->nullable();
            $table->foreign('donation_program_id')->references('id')->on('donation_programs')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
