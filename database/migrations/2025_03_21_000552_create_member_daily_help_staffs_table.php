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
        Schema::create('member_daily_help_staffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('staff_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade');
            $table->time('shift_from')->nullable();
            $table->time('shift_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_daily_help_staffs');
    }
};
