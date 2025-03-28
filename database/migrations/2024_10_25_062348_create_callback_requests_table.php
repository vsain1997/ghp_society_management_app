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
        Schema::create('callback_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained('service_categories')->onDelete('cascade'); // relation to users.id with delete restrict
            $table->foreignId(column: 'request_by')->constrained('users')->onDelete('cascade'); // resident user
            $table->foreignId(column: 'request_to')->constrained('users')->onDelete('cascade'); // service provider user
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade'); // relation to societies.id with delete restrict

            $table->string('aprt_no');
            $table->string('description')->nullable();
            $table->enum('status', ['requested', 'done', 'cancelled'])->default('requested');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callback_requests');
    }
};
