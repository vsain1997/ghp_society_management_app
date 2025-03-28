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
        Schema::create('refer_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('min_budget');
            $table->string('max_budget');
            $table->string('location');
            $table->string('unit_type');
            $table->string('bhk');
            $table->string('property_status');
            $table->string('property_fancing');
            $table->text('remark')->nullable();
            $table->foreignId('society_id')
                ->constrained('societies')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refer_properties');
    }
};
