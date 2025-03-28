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
        Schema::table('blocks', function (Blueprint $table) {
            //
            // Add new columns
            $table->string('property_number')->nullable()->after('id');
            $table->unsignedInteger('floor')->nullable()->after('property_number');
            $table->string('ownership')->nullable()->after('floor');
            $table->string('bhk')->nullable()->after('ownership');
            $table->string('total_floor')->nullable()->after('bhk');

            // Add comments to existing columns
            $table->string('unit_type')->comment('property_type')->change();
            $table->string('unit_size')->comment('property_size')->change();
            $table->string('name')->comment('tower_name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            //
            // Drop new columns
            $table->dropColumn(['property_number', 'floor', 'ownership', 'bhk', 'total_floor']);

            // Remove comments from existing columns
            $table->string('unit_type')->comment(null)->change();
            $table->string('unit_size')->comment(null)->change();
            $table->string('name')->comment(null)->change();
        });
    }
};
