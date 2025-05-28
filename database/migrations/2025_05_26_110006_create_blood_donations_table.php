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
        // Person to person donations in not available here, it will be available in the future
        Schema::create('blood_donations', function (Blueprint $table) {
            // to track donation history of user and blood bank
            $table->id();
            $table->string('blood_type');
            $table->string('quantity');
            $table->date('date_time'); // date_time means date and time blood is donated
            $table->string('location');
            $table->string('contact_number');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // sender
            $table->foreignId('blood_bank_id')->constrained('blood_banks')->cascadeOnDelete(); // receiver
            $table->string('status')->default('pending'); // It can be completed, donated or pending
            $table->string('donated_by')->nullable();
            $table->string('verification_type')->nullable(); // to prevent fake blood donations requests
            // verification_type means what is the identity of the person donating blood
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donations');
    }
};
