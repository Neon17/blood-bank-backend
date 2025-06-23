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
        Schema::table('blood_requests', function (Blueprint $table) {
            //
            $table->boolean('active_status')->default(true); // to see whether Blood Request is active or is fulfilled (by some donor)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            //
            $table->dropColumn('active_status');
        });
    }
};
