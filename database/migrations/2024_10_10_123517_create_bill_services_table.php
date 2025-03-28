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
        Schema::create('bill_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });


        DB::table('bill_services')->insert([
            ['name' => 'Electricity'],
            ['name' => 'Maintenance'],
            ['name' => 'Water'],
            ['name' => 'Gas'],
            ['name' => 'Internet'],
            ['name' => 'Cable TV'],
            ['name' => 'Sewage'],
            ['name' => 'Garbage Collection'],
            ['name' => 'Parking Fees'],
            ['name' => 'Property Tax'],
            ['name' => 'Security Charges'],
            ['name' => 'Society Maintenance'],
            ['name' => 'Housekeeping'],
            ['name' => 'Fire Protection Services'],
            ['name' => 'Lift Maintenance'],
            ['name' => 'Swimming Pool Maintenance'],
            ['name' => 'Gym Membership'],
            ['name' => 'Clubhouse Charges'],
            ['name' => 'Garden Maintenance'],
            ['name' => 'Rainwater Harvesting Maintenance'],
            ['name' => 'Solar Energy Maintenance'],
            ['name' => 'Pest Control'],
            ['name' => 'Generator Backup Fees'],
            ['name' => 'Building Insurance'],
            ['name' => 'Legal Charges'],
            ['name' => 'Community Hall Maintenance'],
            ['name' => 'Common Area Lighting'],
            ['name' => 'Playground Maintenance'],
            ['name' => 'Visitor Parking'],
            ['name' => 'Waste Water Treatment'],
            ['name' => 'Street Sweeping Fees'],
            ['name' => 'Holiday Decorations Maintenance']
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_services');
    }
};
