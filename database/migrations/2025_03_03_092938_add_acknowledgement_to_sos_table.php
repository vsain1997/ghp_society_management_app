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
            //
            $table->dateTime('acknowledged_at')->nullable()->after('deleted_at');
            $table->foreignId('acknowledged_by')->nullable()->after('acknowledged_at')
                ->constrained('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sos', function (Blueprint $table) {
            //
            $table->dropForeign(['acknowledged_by']);
            $table->dropColumn(['acknowledged_at', 'acknowledged_by']);
        });
    }
};
