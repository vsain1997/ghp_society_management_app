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
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->enum('role', [
                'staff',//staff_service_provider
                'staff_security_guard',
                'guest'
            ])->default('guest');
            // $table->foreignId('complaint_category_id')->constrained('complaint_categories')->onDelete('cascade')->nullable();
            $table->bigInteger('complaint_category_id')->unsigned()->nullable();
            $table->foreign('complaint_category_id')->references('id')->on('complaint_categories')->onDelete('cascade');
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->string('card_type')->nullable();
            $table->string('card_number')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('society_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
