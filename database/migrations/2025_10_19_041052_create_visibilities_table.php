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
        Schema::create('visibilities', function (Blueprint $table) {
            $table->id();

            $table->string('type')->enum(['public', 'private', 'protected', 'location'], 'public');
            $table->text('description')->nullable();

            $table->json('settings')->nullable();

            $table->string('status')->enum(['active', 'inactive', 'pending'], 'active');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('radius')->default(10)->nullable();

            $table->foreignId('created_by')->constrained('users')->nullOnDelete()->nullable();
            $table->nullableMorphs('visible');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visibilities');
    }
};
