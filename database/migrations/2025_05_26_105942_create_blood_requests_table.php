<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    // In sprint 1 just do up to donated_by field
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //User should fill form on accepting blood request
        //
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->string('blood_type');
            $table->string('quantity');
            $table->date('date_time'); // date_time means date and time blood is required
            $table->string('location'); // latitude and longitude
            $table->string('contact_number');

            // receiver can be user or blood bank (as per necessity)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->nullable(); // receiver
            $table->foreignId('blood_bank_id')->constrained('blood_banks')->cascadeOnDelete()->nullable(); // receiver
            $table->string('status')->default('pending'); // It can be completed, donated or pending

            $table->string('donated_by')->nullable();

            // to track donation history of user and blood bank
            // if donated by user, donated_by_user has value, and donated_by_blood_banks has null
            // if donated by blood bank, donated_by_user has null, and donated_by_blood_banks has value
            $table->string('donated_by_user')->constrained('users')->nullable();
            $table->string('donated_by_blood_banks')->constrained('blood_banks')->nullable();
            $table->string('verified_by')->nullable(); // to prevent fake blood requests
            //verified_by in blood requests means who asked for the blood, either it can be hospital, healthcare or blood bank
            //full registration of organization should be done and which doctor(if possible) in the future
            $table->string('verification_photo')->nullable();

            //later we can do broadcasting to all country wide or to specific region

            //After seeing blood requests, another blood banks or user can donate blood
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
