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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('role', [
                'admin',
                'resident',
                'service_provider',
                'guest'
            ])->default('guest');
            $table->string('phone');
            $table->string('email');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('society_id')->constrained()->onDelete('cascade');
            $table->foreignId('block_id')->constrained()->onDelete('cascade');

            $table->integer('floor_number');
            $table->string('unit_type');
            $table->string('aprt_no');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
