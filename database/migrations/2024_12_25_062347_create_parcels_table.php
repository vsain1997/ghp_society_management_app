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
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('parcelid');
            $table->string('parcel_name');
            $table->integer('no_of_parcel');
            $table->string('parcel_type');
            $table->date('date')->comment('delivery date');
            $table->time('time')->comment('delivery time');
            $table->string('delivery_name')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->foreignId('parcel_of')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('occupant members of society');
            $table->foreignId('entry_by')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('entry_by_role');
            $table->dateTime('entry_at');
            $table->enum('delivery_option', ['Security Guard', 'Resident', 'Both'])
                ->default('Both')
                ->comment('Security Guard | Resident (admin or resident role members)');
            $table->string('received_by_role')->nullable();
            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->dateTime('received_at')->nullable();
            $table->enum('handover_status', ['pending', 'received', 'delivered'])->default('pending');
            $table->foreignId('handover_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->dateTime('handover_at')->nullable();
            $table->string('resident_delete_status')->nullable();
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
        Schema::dropIfExists('parcels');
    }
};
