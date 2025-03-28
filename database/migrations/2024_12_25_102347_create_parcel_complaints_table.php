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
        Schema::create('parcel_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcel_id')
                ->constrained('parcels')
                ->onDelete('cascade');
            $table->date('date')->comment('complaint date');
            $table->time('time')->comment('complaint time');
            $table->text('description');
            $table->foreignId('complain_of')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('complaint of');
            $table->foreignId('society_id')
                ->constrained('societies')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_complaints');
    }
};
