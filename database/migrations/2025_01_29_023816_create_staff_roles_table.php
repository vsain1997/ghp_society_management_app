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
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->unique();
            $table->timestamps();
        });

        DB::table('staff_roles')->insert([
            ['name' => 'staff', 'label' => 'Maintenance Staff', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'staff_security_guard', 'label' => 'Security Guard', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'staff_cleaner', 'label' => 'Cleaner', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_roles');
    }
};
