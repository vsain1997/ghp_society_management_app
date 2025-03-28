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
        Schema::create('visitor_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamps();
        });

        // Insert predefined visitor types
        DB::table('visitor_types')->insert([
            ['type' => 'Relatives'],
            ['type' => 'Guest'],
            ['type' => 'Friend'],
            ['type' => 'Delivery Person'],
            ['type' => 'Service Provider'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_types');
    }
};
