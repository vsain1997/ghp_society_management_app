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
        Schema::create('complaint_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('image')->nullable();
            $table->timestamps();
        });

        // DB::table('complaint_categories')->insert([
        //     ['name' => 'Lift Operator'],
        //     ['name' => 'Security Related'],
        //     ['name' => 'Plumber'],
        //     ['name' => 'Painter'],
        //     ['name' => 'Mesan Service'],
        // ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_categories');
    }
};
