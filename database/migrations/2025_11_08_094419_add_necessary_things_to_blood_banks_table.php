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
        Schema::table('blood_banks', function (Blueprint $table) {
            $table->string('email')->nullable()->unique();
            $table->string('contact_person')->nullable();
            $table->string('license_number')->nullable(); // Government license
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['hospital', 'independent', 'red_cross']); // Bank type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_banks', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('contact_person');
            $table->dropColumn('license_number');
            $table->dropColumn('is_active');
            $table->dropColumn('type');
        });
    }
};
