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
        Schema::table('sos', function (Blueprint $table) {
            $table->enum('status', ['new', 'acknowledged', 'cancelled'])->default('new')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sos', function (Blueprint $table) {
            $table->enum('status', ['new', 'responding', 'resolved', 'escalated'])->default('new')->change();
        });
    }
};
