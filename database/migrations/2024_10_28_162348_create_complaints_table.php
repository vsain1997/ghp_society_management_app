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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_category_id')->constrained('complaint_categories')->onDelete('cascade');
            $table->foreignId(column: 'complaint_by')->constrained('users')->onDelete('cascade'); // resident
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade'); // relation to societies.id with delete restrict
            $table->foreignId('block_id')->constrained('blocks')->onDelete('cascade'); // relation to blocks.id with delete restrict

            $table->string('block_name');
            $table->string('unit_type');
            $table->string('floor_number');
            $table->string('aprt_no');

            $table->string('area');
            $table->text('description')->nullable();
            $table->text('otp')->nullable();
            $table->enum('status', ['requested', 'assigned', 'in_progress', 'done', 'cancelled'])->default('requested');
            $table->foreignId(column: 'assigned_to')->nullable()->constrained('users')->onDelete('cascade');//staff service provider id
            $table->foreignId(column: 'assigned_by')->nullable()->constrained('users')->onDelete('cascade');//admin id

            $table->datetime('complaint_at')->nullable();
            $table->datetime('assigned_at')->nullable();
            $table->datetime('start_at')->nullable();
            $table->datetime('resolved_or_cancelled_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
