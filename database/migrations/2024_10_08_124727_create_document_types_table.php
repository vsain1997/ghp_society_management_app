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
        // if (!Schema::hasTable('document_types')) {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255);
            $table->timestamps();
        });

        // Pre-insert 50 document types for Indian citizens
        DB::table('document_types')->insert([
            ['type' => 'Aadhaar Card'],
            ['type' => 'Voter ID Card'],
            ['type' => 'PAN Card'],
            ['type' => 'Passport'],
            ['type' => 'Driving License'],
            ['type' => 'Ration Card'],
            ['type' => 'Electricity Bill'],
            ['type' => 'Water Bill'],
            ['type' => 'Gas Bill'],
            ['type' => 'Birth Certificate'],
            ['type' => 'Property Tax Receipt'],
            ['type' => 'Medical Certificate'],
            ['type' => 'Disability Certificate'],
            ['type' => 'Caste Certificate'],
            ['type' => 'Domicile Certificate'],
            ['type' => 'NOC from Society'],
            ['type' => 'Police Verification'],
            ['type' => 'Death Certificate'],
            ['type' => 'Others'],
        ]);
        // }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
