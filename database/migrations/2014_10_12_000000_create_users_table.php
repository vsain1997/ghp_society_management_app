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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique();
            $table->enum('role', [
                'super_admin',
                'admin',
                'resident',
                'service_provider',
                'staff',//staff_service_provider
                'staff_security_guard',
                'guest'
            ])->default('guest');

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('inactive');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('otp')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->timestamp('otp_expire_time')->nullable();
            $table->string('image')->nullable();
            $table->string('phone')->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
