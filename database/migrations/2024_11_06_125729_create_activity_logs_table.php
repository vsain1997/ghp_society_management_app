<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // General Information
            $table->enum('event_type', ['info', 'warning', 'error', 'debug', 'audit', 'report'])->comment('Type of event');
            $table->string('activity_name')->comment('Brief activity name');
            $table->text('description')->nullable()->comment('Detailed description');

            // User & Role Details
            $table->unsignedBigInteger('user_id')->nullable()->comment('User who performed the action');
            $table->string('user_role', 50)->nullable()->comment('Role of user');
            $table->unsignedBigInteger('society_id')->comment('ID of society');

            // Changes Tracking
            $table->text('before_data')->nullable()->comment('Data before change, serialized JSON');
            $table->text('after_data')->nullable()->comment('Data after change, serialized JSON');
            $table->text('request_data')->nullable()->comment('Request payload, serialized JSON');

            // Device & Location
            $table->string('ip_address', 45)->nullable()->comment('User IP address');
            $table->text('user_agent')->nullable()->comment('User agent');
            $table->string('location', 255)->nullable()->comment('Geolocation');

            // Timestamp Details
            $table->timestamps();

            // Additional Metadata
            $table->string('route_name')->nullable()->comment('Triggered Laravel route');
            $table->string('model_type')->nullable()->comment('Type of model related');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID of model related');
            $table->enum('status', ['success', 'failed', 'pending'])->default('success')->comment('Activity outcome');
            $table->unsignedTinyInteger('severity_level')->default(0)->comment('Severity level');

            // Indexing for performance
            $table->index('event_type');
            $table->index('user_id');
            $table->index('user_role');
            $table->index('society_id');
            $table->index('created_at');
            $table->index(['model_type', 'model_id']);
            $table->index('status');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};


/**
 * -----------------------------------------------------------------------------
 * Severity Levels for Activity Logs
 * -----------------------------------------------------------------------------
 *
 * These severity levels are used to classify log entries in the activity_logs
 * table, helping to prioritize debugging, generate reports, and set up alerts.
 * Each level is assigned a numeric value for easy filtering and sorting.
 *
 * Severity Level | Description                       | Suggested Use Cases
 * ---------------------------------------------------------------------------
 * 0              | Informational                     | Routine log entries, general actions (e.g., login, view).
 * 1              | Low                               | Minor updates, non-critical user actions.
 * 2              | Moderate                          | Important actions, non-critical data changes.
 * 3              | High                              | Unusual actions, recoverable errors, validation failures.
 * 4              | Very High                         | Significant issues, unexpected state changes.
 * 5              | Critical                          | Errors requiring immediate attention, security events.
 * 6              | Emergency                         | System or application failure, data loss scenarios.
 *
 * Example Usage in Logs:
 * ----------------------------------------------------------------------------
 * - Informational (0): "User viewed the notice details page."
 * - Low (1): "User updated their profile."
 * - Moderate (2): "Notice created successfully."
 * - High (3): "User attempted action with invalid input."
 * - Very High (4): "System detected multiple login attempts."
 * - Critical (5): "Database connection lost, retrying."
 * - Emergency (6): "Data corruption detected in notices table."
 *
 * Using these severity levels enables more efficient monitoring, debugging,
 * and prioritization of issues within the application.
 */
