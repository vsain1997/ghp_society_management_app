<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('visitor_checkin_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade'); // Foreign key to visitors table
            $table->enum('status', ['requested', 'allowed', 'checked_in', 'not_allowed', 'not_responded', 'checked_out']);
            $table->datetime('requested_at')->nullable();
            $table->datetime('checkin_at')->nullable();
            $table->datetime('checkout_at')->nullable();
            $table->foreignId('request_by')->nullable()->constrained('users')->onDelete('set null'); // Guard who checked in
            $table->foreignId('checkin_by')->nullable()->constrained('users')->onDelete('set null'); // Guard who checked in
            $table->foreignId('checkout_by')->nullable()->constrained('users')->onDelete('set null'); // Guard who checked out
            $table->foreignId('visitor_of')->nullable()->constrained('users')->onDelete('set null'); // Resident user ID
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade'); // relation to societies.id with delete restrict
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_checkin_details');
    }
};
