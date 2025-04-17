<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            $table->enum('checkin_type', ['qr','manual'])->after('checkin_at')->nullable();
            $table->enum('checkout_type', ['qr','manual'])->after('checkout_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            //
        });
    }
};
