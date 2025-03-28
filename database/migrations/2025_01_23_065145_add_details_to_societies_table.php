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
        Schema::table('societies', function (Blueprint $table) {
            //
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pin', 10)->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('registration_num')->nullable();
            $table->string('type')->nullable();
            $table->string('total_area')->nullable();
            $table->unsignedInteger('total_towers')->nullable();
            $table->text('amenities')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            //
            $table->dropColumn([
                'city',
                'state',
                'pin',
                'contact',
                'email',
                'registration_num',
                'type',
                'total_area',
                'total_towers',
                'amenities',
            ]);
        });
    }
};
