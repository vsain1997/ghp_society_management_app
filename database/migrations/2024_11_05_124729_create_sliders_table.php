<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->string('title');
            $table->string('sub_title');
            $table->string('image');
            $table->integer('society_id')->default(0);
            $table->timestamps();
        });

        // Insert default data
        DB::table('sliders')->insert([
            [
                'id' => 1,
                'location' => 'NAVI MUMBAI',
                'title' => 'Sudhan Apartment',
                'sub_title' => 'Invest in our upcoming project for better future',
                'image' => 'sliders/slider-1.jpeg',
                'society_id' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'location' => 'DELHI',
                'title' => 'Vedanta Apartment',
                'sub_title' => 'Invest in our upcoming project for better future',
                'image' => 'sliders/slider-2.jpeg',
                'society_id' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'location' => 'JAIPUR',
                'title' => 'Quaint Stay Apartment',
                'sub_title' => 'Invest in our upcoming project for better future',
                'image' => 'sliders/slider-3.jpeg',
                'society_id' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
