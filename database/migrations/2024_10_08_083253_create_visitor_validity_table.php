<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('visitor_validities', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamps();
        });

        // Insert predefined visitor types
        DB::table('visitor_validities')->insert([
            ['type' => '12 Hours'],
            ['type' => '24 Hours'],
            ['type' => '7 Days'],
            ['type' => '15 Days'],
            ['type' => '30 Days'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_validities');
    }
};
