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
            $table->foreignId('block_id')->nullable()->change();
            $table->integer('floor')->nullable()->change();
            $table->string('unit_no')->nullable()->change();
            $table->string('unit_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sos', function (Blueprint $table) {
            $table->foreignId('block_id')->nullable(false)->change();
            $table->integer('floor')->nullable(false)->change();
            $table->string('unit_no')->nullable(false)->change();
            $table->string('unit_type')->nullable(false)->change();
        });
    }
};
