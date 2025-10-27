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
        Schema::table('donation_programs', function (Blueprint $table) {
            $table->foreignId('blood_bank_id')->nullable()->constrained('blood_banks')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation_programs', function (Blueprint $table) {
            $table->dropForeign(['blood_bank_id']);
            $table->dropColumn('blood_bank_id');
        });
    }
};
