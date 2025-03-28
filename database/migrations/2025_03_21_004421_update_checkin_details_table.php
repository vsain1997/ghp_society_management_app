<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            $table->foreignId('by_daily_help')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('daily_help_for_member')->nullable()->constrained('users')->onDelete('cascade');
        });

        // Modify the ENUM field while keeping existing values
        DB::statement("ALTER TABLE checkin_details MODIFY COLUMN status ENUM('requested', 'allowed', 'checked_in', 'not_allowed', 'not_responded', 'checked_out', 'in', 'out') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            $table->dropForeign(['by_daily_help']);
            $table->dropColumn('by_daily_help');

            $table->dropForeign(['daily_help_for_member']);
            $table->dropColumn('daily_help_for_member');
        });

        // Revert the ENUM field change (remove 'in', 'out')
        DB::statement("ALTER TABLE checkin_details MODIFY COLUMN status ENUM('requested', 'allowed', 'checked_in', 'not_allowed', 'not_responded', 'checked_out') NOT NULL");
    }
};
