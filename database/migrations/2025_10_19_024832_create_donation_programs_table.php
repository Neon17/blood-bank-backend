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
        Schema::create('donation_programs', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description');
            $table->date('program_date');
            $table->time('program_time');

            $table->string('city')->nullable()->default('Kathmandu');
            $table->string('state')->nullable();
            $table->string('country')->nullable()->default('Nepal');
            $table->string('address')->nullable();

            $table->string('venue')->nullable();
            $table->string('blood_group')->default('Any');
            $table->string('quantity')->nullable();

            $table->decimal('latitude', 10, 8)->default(27.7172);
            $table->decimal('longitude', 11, 8)->default(85.3240);

            $table->string('contact_number');

            $table->string('status')->default('pending');
            $table->string('verification_status')->default('pending');

            $table->json('tags')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);

            $table->foreignId('user_id')->constrained('users')->nullOnDelete()->nullable();

            $table->string('organizer')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_programs');
    }
};
