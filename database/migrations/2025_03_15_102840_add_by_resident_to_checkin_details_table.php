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
        Schema::table('checkin_details', function (Blueprint $table) {
            //
            $table->foreignId('by_resident')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade')
                ->after('parcel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkin_details', function (Blueprint $table) {
            //
            $table->dropForeign(['by_resident']);
            $table->dropColumn('by_resident');
        });
    }
};
