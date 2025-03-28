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
        Schema::create('visitor_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade'); // Foreign key to visitors table
            $table->tinyInteger('rating')->nullable()->unsigned()->comment('Rating out of 5'); // Rating, e.g., 1-5
            $table->text('feedback')->nullable()->comment('Optional text feedback'); // Detailed feedback
            $table->foreignId('feedback_by')->constrained('users')->onDelete('cascade'); // Who submitted feedback (could be a resident or staff)
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_feedback');
    }
};
