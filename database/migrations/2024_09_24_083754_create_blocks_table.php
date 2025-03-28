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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('unit_type');
            $table->integer('unit_size')->default(0);
            $table->integer('unit_qty')->default(0);
            $table->string('name');
            $table->integer('total_units');
            $table->unsignedBigInteger('society_id'); // Foreign key

            // Define the relationship with the societies table
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
