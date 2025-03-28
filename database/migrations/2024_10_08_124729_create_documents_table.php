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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('request_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('request_by_role')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('uploaded_by_role')->nullable();
            $table->foreignId('society_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['requested', 'uploaded'])->default('requested');
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->onDelete('set null');
            $table->string('file_type')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
