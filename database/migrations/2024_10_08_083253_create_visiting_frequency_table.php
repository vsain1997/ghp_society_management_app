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
        Schema::create('visiting_frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('frequency');
            $table->timestamps();
        });

        // Insert predefined frequency options
        DB::table('visiting_frequencies')->insert([
            ['frequency' => 'Daily'],
            ['frequency' => 'Weekly'],
            ['frequency' => 'Monthly'],
            ['frequency' => 'Yearly'],
            ['frequency' => 'Frequently'],
            ['frequency' => 'Occasionally'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visiting_frequencies');
    }
};
