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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('role')->default('guest')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back to enum if needed
            $table->enum('role', [
                'super_admin',
                'admin',
                'resident',
                'service_provider',
                'staff', // staff_service_provider
                'staff_security_guard',
                'guest'
            ])->default('guest')->change();

        });
    }
};
