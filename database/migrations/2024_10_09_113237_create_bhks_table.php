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
        Schema::create('bhks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Insert pre-defined data
        DB::table('bhks')->insert([
            ['name' => 'BHK 1'],
            ['name' => 'BHK 2'],
            ['name' => 'BHK 3'],
            ['name' => 'BHK 4'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bhks');
    }
};
