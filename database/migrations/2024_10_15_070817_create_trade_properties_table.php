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
        Schema::create('trade_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')
                ->constrained('blocks')
                ->onDelete('cascade');
            $table->integer('floor');
            $table->enum('type', ['rent', 'sell']);
            $table->string('unit_type');
            $table->string('unit_number')->nullable();
            $table->string('bhk')->nullable();
            $table->float('area')->nullable();

            // for rent
            $table->decimal('rent_per_month', 15, 2)->nullable();
            $table->decimal('security_deposit', 15, 2)->nullable();
            // for sell
            $table->decimal('house_price', 15, 2)->nullable();
            $table->decimal('upfront', 15, 2)->nullable();

            $table->date('available_from_date')->nullable();
            $table->text('amenities')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->foreignId('society_id')
                ->constrained('societies')
                ->onDelete('cascade');
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_properties');
    }
};
